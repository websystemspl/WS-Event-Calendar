<?php

namespace WsEventCalendar\App;

use WsEventCalendar\App\Events\EventsManager;

class Assets
{
  public function __construct()
  {
    \add_action('admin_enqueue_scripts', [$this, "addAdminStylesAndScripts"], 10);
    \add_action('wp_enqueue_scripts', [$this, "addStylesAndScripts"], 10);
  }
  public function addStylesAndScripts()
  {
    /* Styles */
    \wp_enqueue_style('ws-events-styles', WS_EVENT_CALENDAR_PLUGIN_DIR_URL . 'assets/css/frontend/events-style.css');

    /* Scripts */
  }

  public function addAdminStylesAndScripts($hook)
  {
    global $post;
    $eventManager = new EventsManager;
    if (($hook === 'post-new.php' || $hook === 'post.php') && $post->post_type === $eventManager->getSlug()) {
      \wp_enqueue_script('ws-events-styles', WS_EVENT_CALENDAR_PLUGIN_DIR_URL . 'assets/js/admin/events.js');
    }
  }
}
