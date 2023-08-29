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
    \add_shortcode('eventList', [$this, 'displayEventList']);
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

  function singleEventTemplate($template) {
      global $post;
      if ( $post->post_type == 'wsec-event' ) {
          if ( file_exists( WS_EVENT_CALENDAR_PLUGIN_DIR_PATH . 'App/Events/single-wsec-event.php' ) ) {
              return WS_EVENT_CALENDAR_PLUGIN_DIR_PATH . 'App/Events/single-wsec-event.php';
          }
      }
      return $template;
  }
}
