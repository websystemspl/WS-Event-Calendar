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
    //$this->displayEventList();
    \add_shortcode( 'eventList', [$this,'displayEventList']);
    //\add_shortcode( 'singleEvent', [$this,'displaySingleEvent'] );
  }

  public function displayEventList(){
    $eventManager = new EventsManager;
    $eventsList = $eventManager->getEvents(5);
    $events = new Events;
    return $events->displayEventList($eventsList);
  }
  public function displaySingleEvent($event){
    $events = new Events;
    return $events->displaySingleEvent($event);
  }
}
