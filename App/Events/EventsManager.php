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
      'name'                  => esc_html__('Events', 'web-systems-events-calendar'),
      'singular_name'         => esc_html__('Event', 'web-systems-events-calendar'),
      'menu_name'             => esc_html__('Events', 'web-systems-events-calendar'),
      'name_admin_bar'        => esc_html__('Event', 'web-systems-events-calendar'),
      'add_new'               => esc_html__('Add New', 'web-systems-events-calendar'),
      'add_new_item'          => esc_html__('Add New Event', 'web-systems-events-calendar'),
      'new_item'              => esc_html__('New Event', 'web-systems-events-calendar'),
      'edit_item'             => esc_html__('Edit Event', 'web-systems-events-calendar'),
      'view_item'             => esc_html__('View Event', 'web-systems-events-calendar'),
      'all_items'             => esc_html__('All Events', 'web-systems-events-calendar'),
      'search_items'          => esc_html__('Search Events', 'web-systems-events-calendar'),
      'parent_item_colon'     => esc_html__('Parent Events:', 'web-systems-events-calendar'),
      'not_found'             => esc_html__('No events found.', 'web-systems-events-calendar'),
      'not_found_in_trash'    => esc_html__('No events found in Trash.', 'web-systems-events-calendar'),
      'archives'              => esc_html__('Event archives',  'web-systems-events-calendar'),
      'filter_items_list'     => esc_html__('Filter events list',  'web-systems-events-calendar'),
      'items_list_navigation' => esc_html__('Events items navigation', 'web-systems-events-calendar'),
      'items_list'            => esc_html__('Events list', 'web-systems-events-calendar')
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
      esc_html__('Event Settings', 'web-systems-events-calendar'),
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
				<th><label for="' . esc_attr($this->getSubTitleFieldName()) . '">' . esc_html__('Event Title', 'web-systems-events-calendar') . '</label></th>
<td><input required type="text" id="' . esc_attr($this->getSubTitleFieldName()) . '" name="meta[' . esc_attr($this->getSubTitleFieldName()) . ']" value="' . esc_attr($subTitleValue) . '" class="regular-text"></td>
			</tr>
			<tr>
				<th><label for="' . $this->geteventStartDateFieldName() . '">' . esc_html__('Start Date', 'web-systems-events-calendar') . '</label></th>
<td><input required type="datetime-local" id="' . esc_attr($this->geteventStartDateFieldName()) . '" name="meta[' . esc_attr($this->geteventStartDateFieldName()) . ']" value="' . esc_attr($eventStartDateValue) . '"></td>
			</tr>
      <tr>
        <th><label for="' . $this->getEventEndDateFieldName() . '">' . esc_html__('End Date', 'web-systems-events-calendar') . '</label></th>
<td><input required type="datetime-local" id="' . esc_attr($this->getEventEndDateFieldName()) . '" name="meta[' . esc_attr($this->getEventEndDateFieldName()) . ']" value="' . esc_attr($eventEndDateValue) . '"></td>
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

    if (!isset($_POST['wsec_nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['wsec_nonce']), basename(__FILE__))) {
      return 'nonce not verified';
    }

    foreach (array_map('sanitize_text_field', $_POST['meta']) as $key => $meta) {
      update_post_meta($post_id, $key, $meta);
    }
  }
}
