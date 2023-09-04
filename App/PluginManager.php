<?php

namespace WsEventCalendar\App;

use WsEventCalendar\App\Assets;
use WsEventCalendar\App\Events\EventsManager;
use WsEventCalendar\App\Events\Events;

class PluginManager
{
  public function run()
  {
    new Assets;
    $eventManager = new EventsManager;
    $eventManager->run();
    add_filter('single_template', [$this, 'singleEventTemplate']);
    add_filter('archive_template', [$this, 'eventsTemplate']);
    \add_shortcode('events', [$this, 'displayEventList']);
    //\add_shortcode( 'singleEvent', [$this,'displaySingleEvent'] );
  }

  public function displayEventList($atts)
  {
    $events = new Events;
    return $events->displayEventList((isset($atts['pagination']) && $atts['pagination'] === "true") ? true : false, (isset($atts['post_per_page'])) ? $atts['post_per_page'] : PHP_INT_MAX);
  }

  public function displaySingleEvent($event)
  {
    $events = new Events;
    return $events->displaySingleEvent($event);
  }

  function singleEventTemplate($template)
  {
    global $post;
    $eventManager = new EventsManager;
    if ($post->post_type == $eventManager->getSlug()) {
      if (file_exists(WS_EVENT_CALENDAR_PLUGIN_DIR_PATH . 'App/Events/templates/single-wsec-event.php')) {
        return WS_EVENT_CALENDAR_PLUGIN_DIR_PATH . 'App/Events/templates/single-wsec-event.php';
      }
    }
    return $template;
  }

  function eventsTemplate($template)
  {
    global $post;
    $eventManager = new EventsManager;
    if ($post->post_type == $eventManager->getSlug()) {
      if (file_exists(WS_EVENT_CALENDAR_PLUGIN_DIR_PATH . 'App/Events/templates/archive-wsec-event.php')) {
        return WS_EVENT_CALENDAR_PLUGIN_DIR_PATH . 'App/Events/templates/archive-wsec-event.php';
      }
    }

    return $template;
  }
}
