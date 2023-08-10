<?php

namespace WsEventCalendar\App;

use WsEventCalendar\App\Assets;
use WsEventCalendar\App\Events\EventsManager;

class PluginManager
{
  public function run()
  {
    new Assets;
    new EventsManager;
  }
}
