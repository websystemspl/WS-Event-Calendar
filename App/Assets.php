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


    /* Scripts */
  }
}
