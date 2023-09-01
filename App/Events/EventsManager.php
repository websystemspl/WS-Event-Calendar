<?php

namespace WsEventCalendar\App\Events;

use DateTime;

class EventsManager
{
  private $slug = 'wsec-event';
  private $metaboxId = 'wsec_single_event_metabox';
  private $subTitleFieldName = "wsec_subtitle";
  private $eventStartDateFieldName = "wsec_start_event_date";
  private $eventEndDateFieldName = 'wsec_end_event_date';
  private $eventLinkFieldname = 'wsec_linked_post';

  public function getSlug()
  {
    return $this->slug;
  }

  public function getMetaboxId()
  {
    return $this->metaboxId;
  }

  public function getSubTitleFieldName()
  {
    return $this->subTitleFieldName;
  }
  public function geteventStartDateFieldName()
  {
    return $this->eventStartDateFieldName;
  }
  public function getEventEndDateFieldName()
  {
    return $this->eventEndDateFieldName;
  }
  public function getEventLinkFieldname()
  {
    return $this->eventLinkFieldname;
  }

  public function run()
  {
    add_action('init', [$this, 'registerEventPostType']);
    add_action('add_meta_boxes', [$this, 'registerSingleEventFields']);
    add_action('pre_post_update', [$this, 'saveCustomFields'], 10, 2);
  }

  private function getLiveAndFutureEventsArgs(int $numberOfEvents, int $offset): array
  {
    return [
      'post_type'      => $this->getSlug(),
      'post_status'    => 'publish',
      'posts_per_page' => $numberOfEvents,
      'offset' => $offset,
      'meta_key' => $this->geteventStartDateFieldName(),
      'orderby' => 'meta_value',
      'order' => 'ASC',
      'meta_query' => [
        [
          'key' => $this->getEventEndDateFieldName(),
          'value' => current_time('Y-m-d h:i:s'),
          'compare' => '>=',
          'type' => 'DATETIME'
        ]
      ],
    ];
  }

  private function getPastEventsArgs(int $numberOfEvents): array
  {
    return [
      'post_type'      => $this->getSlug(),
      'post_status'    => 'publish',
      'posts_per_page' => $numberOfEvents,
      'meta_key' => $this->getEventEndDateFieldName(),
      'orderby' => 'meta_value',
      'order' => 'DESC',
      'meta_query' => [
        [
          'key' => $this->getEventEndDateFieldName(),
          'value' => current_time('Y-m-d h:i:s'),
          'compare' => '<',
          'type' => 'DATETIME'
        ]
      ],
    ];
  }

  public function getNumberOfAllLiveAndFutureEvents()
  {
    $liveAndFutureEvents = \get_posts($this->getLiveAndFutureEventsArgs(PHP_INT_MAX, 0));
    return count($liveAndFutureEvents);
  }

  public function getEvents(int $numberOfEvents, int $offset)
  {
    $eventsCollection = new Events();
    $liveAndFutureEvents = \get_posts($this->getLiveAndFutureEventsArgs($numberOfEvents, $offset));
    if (count($liveAndFutureEvents) < $numberOfEvents) {
      $pastEvents = \get_posts($this->getPastEventsArgs($numberOfEvents - count($liveAndFutureEvents)));
    } else {
      $pastEvents = [];
    }
    foreach ([$liveAndFutureEvents, $pastEvents] as $events) {
      foreach ($events as $event) {
        $meta = \get_post_meta($event->ID);
        $image = get_the_post_thumbnail_url($event->ID);
        $subTitle = $meta[$this->getSubTitleFieldName()][0];
        $startDate = $meta[$this->geteventStartDateFieldName()][0];
        $endDate = $meta[$this->getEventEndDateFieldName()][0];
        $eventsCollection->add_event(new Event(
          $event->ID,
          $event->post_title,
          $image,
          '' === $subTitle ? null : $subTitle,
          $event->post_content,
          new DateTime($startDate),
          new DateTime($endDate),
          '',
        ));
      }
    }
    return $eventsCollection->get_items();
  }

  public function registerEventPostType(): void
  {
    $labels = [
      'name'                  => __('Events', 'web-systems-events-calendar'),
      'singular_name'         => __('Event', 'web-systems-events-calendar'),
      'menu_name'             => __('Events', 'web-systems-events-calendar'),
      'name_admin_bar'        => __('Event', 'web-systems-events-calendar'),
      'add_new'               => __('Add New', 'web-systems-events-calendar'),
      'add_new_item'          => __('Add New Event', 'web-systems-events-calendar'),
      'new_item'              => __('New Event', 'web-systems-events-calendar'),
      'edit_item'             => __('Edit Event', 'web-systems-events-calendar'),
      'view_item'             => __('View Event', 'web-systems-events-calendar'),
      'all_items'             => __('All Events', 'web-systems-events-calendar'),
      'search_items'          => __('Search Events', 'web-systems-events-calendar'),
      'parent_item_colon'     => __('Parent Events:', 'web-systems-events-calendar'),
      'not_found'             => __('No events found.', 'web-systems-events-calendar'),
      'not_found_in_trash'    => __('No events found in Trash.', 'web-systems-events-calendar'),
      'archives'              => __('Event archives',  'web-systems-events-calendar'),
      'filter_items_list'     => __('Filter events list',  'web-systems-events-calendar'),
      'items_list_navigation' => __('Events items navigation', 'web-systems-events-calendar'),
      'items_list'            => __('Events list', 'web-systems-events-calendar')
    ];

    $supports = [
      'title',
      'editor',
      'thumbnail',
    ];

    $args = [
      'labels'             => $labels,
      'public'             => true,
      'publicly_queryable' => true,
      'show_ui'            => true,
      'show_in_menu'       => true,
      'query_var'          => true,
      'rewrite'            => ['slug' => $this->getSlug()],
      'capability_type'    => 'post',
      'has_archive'        => true,
      'hierarchical'       => false,
      'menu_position'      => null,
      'menu_icon'          => 'dashicons-tickets-alt',
      'supports'           => $supports
    ];



    \register_post_type($this->getSlug(), $args);
  }

  public function registerSingleEventFields()
  {
    add_meta_box(
      $this->getMetaboxId(),
      __('Event Settings', 'web-systems-events-calendar'),
      [$this, 'renderSingleEventMetaBox'],
      $this->getSlug(),
      'normal',
      'high'
    );
  }

  public function renderSingleEventMetaBox($post)
  {
    $meta = get_post_meta($post->ID);
    $subTitleValue = (isset($meta[$this->getSubTitleFieldName()][0]) ? $meta[$this->getSubTitleFieldName()][0] : '');
    $eventStartDateValue = (isset($meta[$this->geteventStartDateFieldName()][0]) ? $meta[$this->geteventStartDateFieldName()][0] : '');
    $eventEndDateValue = (isset($meta[$this->getEventEndDateFieldName()][0]) ? $meta[$this->getEventEndDateFieldName()][0] : '');

    // Use nonce for verification to secure data sending
    wp_nonce_field(basename(__FILE__), 'wsec_nonce');

    $html = '<table class="form-table">
		<tbody>
			<tr>
				<th><label for="' . $this->getSubTitleFieldName() . '">' . __('Event Title', 'web-systems-events-calendar') . '</label></th>
				<td><input type="text" id="' . $this->getSubTitleFieldName() . '" name="meta[' . $this->getSubTitleFieldName() . ']" value="' . $subTitleValue . '" class="regular-text"></td>
			</tr>
			<tr>
				<th><label for="' . $this->geteventStartDateFieldName() . '">' . __('Start Date', 'web-systems-events-calendar') . '</label></th>
        <td><input type="datetime-local" id="' . $this->geteventStartDateFieldName() . '" name="meta[' . $this->geteventStartDateFieldName() . ']" value="' . $eventStartDateValue . '"></td>
			</tr>
      <tr>
        <th><label for="' . $this->getEventEndDateFieldName() . '">' . __('End Date', 'web-systems-events-calendar') . '</label></th>
        <td><input type="datetime-local" id="' . $this->getEventEndDateFieldName() . '" name="meta[' . $this->getEventEndDateFieldName() . ']" value="' . $eventEndDateValue . '"></td>
      </tr>
		</tbody>
	</table>';
    echo $html;
  }

  public function saveCustomFields($post_id)
  {
    $post = get_post($post_id);
    if ($post->post_type != $this->getSlug()) {
      return;
    }

    if (!isset($_POST['wsec_nonce']) || !wp_verify_nonce($_POST['wsec_nonce'], basename(__FILE__))) {
      return 'nonce not verified';
    }

    foreach ($_POST['meta'] as $key => $meta) {
      update_post_meta($post_id, $key, $meta);
    }
  }
}
