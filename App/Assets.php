<?php

namespace WsEventCalendar\App;

class Assets
{
  public function __construct()
  {
    \add_action('wp_enqueue_scripts', [$this, "addStylesAndScripts"], 10);
  }
  public function addStylesAndScripts()
  {
    /* Styles */
    \wp_enqueue_style('ws-events-styles', WS_EVENT_CALENDAR_PLUGIN_DIR_URL . 'assets/css/frontend/events-style.css');

    /* Scripts */
  }
}
