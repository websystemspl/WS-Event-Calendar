<?php

namespace WsEventCalendar\App\Events;

class EventsManager
{
  private $slug = 'wsec_event';
  private $metaboxId = 'wsec_single_event_metabox';
  private $subTitleFieldName = "wsec_subtitle";
  private $eventDateFieldName = "wsec_event_date";
  private $eventCounterDateFieldName = 'wsec_counter_date';
  private $eventExpirationDateFieldName = 'wsec_expiration_date';
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
  public function getEventDateFieldName()
  {
    return $this->eventDateFieldName;
  }
  public function geteEventCounterDateFieldName()
  {
    return $this->eventCounterDateFieldName;
  }
  public function getEventExpirationDateFieldName()
  {
    return $this->eventExpirationDateFieldName;
  }
  public function getEventLinkFieldname()
  {
    return $this->eventLinkFieldname;
  }


  public function __construct()
  {
    add_action('init', [$this, 'registerEventPostType']);
    add_action('add_meta_boxes', [$this, 'registerSingleEventFields']);
    add_action('pre_post_update', [$this, 'saveCustomFields'], 10, 2);
  }

  public function registerEventPostType(): void
  {
    $labels = [
      'name'                  => __('Events', 'web-systems-events-calendar-banner'),
      'singular_name'         => __('Event', 'web-systems-events-calendar-banner'),
      'menu_name'             => __('Events', 'web-systems-events-calendar-banner'),
      'name_admin_bar'        => __('Event', 'web-systems-events-calendar-banner'),
      'add_new'               => __('Add New', 'web-systems-events-calendar-banner'),
      'add_new_item'          => __('Add New Event', 'web-systems-events-calendar-banner'),
      'new_item'              => __('New Event', 'web-systems-events-calendar-banner'),
      'edit_item'             => __('Edit Event', 'web-systems-events-calendar-banner'),
      'view_item'             => __('View Event', 'web-systems-events-calendar-banner'),
      'all_items'             => __('All Events', 'web-systems-events-calendar-banner'),
      'search_items'          => __('Search Events', 'web-systems-events-calendar-banner'),
      'parent_item_colon'     => __('Parent Events:', 'web-systems-events-calendar-banner'),
      'not_found'             => __('No events found.', 'web-systems-events-calendar-banner'),
      'not_found_in_trash'    => __('No events found in Trash.', 'web-systems-events-calendar-banner'),
      'archives'              => __('Event archives',  'web-systems-events-calendar-banner'),
      'filter_items_list'     => __('Filter events list',  'web-systems-events-calendar-banner'),
      'items_list_navigation' => __('Events items navigation', 'web-systems-events-calendar-banner'),
      'items_list'            => __('Events list', 'web-systems-events-calendar-banner')
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
    $eventDate = (isset($meta[$this->getEventDateFieldName()][0]) ? $meta[$this->getEventDateFieldName()][0] : '');
    $eventCounterDateValue = (isset($meta[$this->geteEventCounterDateFieldName()][0]) ? $meta[$this->geteEventCounterDateFieldName()][0] : '');
    $eventExpirationDateValue = (isset($meta[$this->getEventExpirationDateFieldName()][0]) ? $meta[$this->getEventExpirationDateFieldName()][0] : '');
    // Use nonce for verification to secure data sending
    wp_nonce_field(basename(__FILE__), 'wsec_nonce');

    $html = '<table class="form-table">
		<tbody>
			<tr>
				<th><label for="' . $this->getSubTitleFieldName() . '">' . __('Event Title', 'web-systems-events-calendar') . '</label></th>
				<td><input type="text" id="' . $this->getSubTitleFieldName() . '" name="meta[' . $this->getSubTitleFieldName() . ']" value="' . $subTitleValue . '" class="regular-text"></td>
			</tr>
			<tr>
				<th><label for="' . $this->getEventDateFieldName() . '">' . __('Event Date', 'web-systems-events-calendar') . '</label></th>
        <td><input type="datetime-local" id="' . $this->getEventDateFieldName() . '" name="meta[' . $this->getEventDateFieldName() . ']" value="' . $eventDate . '"></td>
			</tr>
      <tr>
        <th><label for="' . $this->geteEventCounterDateFieldName() . '">' . __('Counter Date', 'web-systems-events-calendar') . '</label></th>
        <td><input type="datetime-local" id="' . $this->geteEventCounterDateFieldName() . '" name="meta[' . $this->geteEventCounterDateFieldName() . ']" value="' . $eventCounterDateValue . '"></td>
      </tr>
      <tr>
        <th><label for="' . $this->getEventExpirationDateFieldName() . '">' . __('Expiration Date', 'web-systems-events-calendar') . '</label></th>
        <td><input type="datetime-local" id="' . $this->getEventExpirationDateFieldName() . '" name="meta[' . $this->getEventExpirationDateFieldName() . ']" value="' . $eventExpirationDateValue . '"></td>
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
