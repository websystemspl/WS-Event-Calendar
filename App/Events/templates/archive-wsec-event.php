<?php

use WsEventCalendar\App\Events\Events;

/*
Template Name: Event
Template Post Type: wsec-event
*/

if (get_header('desktop') === false) {
  get_header();
} else {
  get_header('desktop');
}

$events = new Events;
echo "<main class='main'>";
echo $events->displayEventList(true, "10");
echo "</main>";

get_footer();
