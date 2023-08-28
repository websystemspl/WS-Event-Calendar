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
    \add_shortcode('eventList', [$this, 'displayEventList']);
    //\add_shortcode( 'singleEvent', [$this,'displaySingleEvent'] );
  }

  public function displayEventList($atts)
  {
    $events = new Events;
    return $events->displayEventList((isset($atts['pagination']) && $atts['pagination'] === "true") ? true : false, (isset($atts['post_per_page'])) ? $atts['post_per_page'] : "0");
  }
  public function displaySingleEvent($event)
  {
    $events = new Events;
    return $events->displaySingleEvent($event);
  }
}
