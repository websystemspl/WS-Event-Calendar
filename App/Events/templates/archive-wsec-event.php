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
echo esc_html("<main class='main'>");
echo wp_kses_post($events->displayEventList(true, "10"));
echo esc_html("</main>");

get_footer();
