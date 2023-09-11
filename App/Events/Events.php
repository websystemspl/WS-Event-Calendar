<?php

namespace WsEventCalendar\App\Events;

use DateTime;
use WsEventCalendar\App\Events\EventsManager;

class Events
{

    private array $items = [];

    public function get_items(): array
    {
        return $this->items;
    }

    protected function add_item(Event $item): void
    {
        $this->items[] = $item;
    }

    public function add_event(Event $event): void
    {
        $this->add_item($event);
    }

    public function displayEventList($pagination, $postPerPage)
    {
        $eventManager = new EventsManager;
        $postPerPage = intval($postPerPage);
        $paged = intval((get_query_var('paged')) ? get_query_var('paged') : 1);
        if ($paged === 1) {
            $offset = 0;
        } else {
            $offset = $postPerPage * ($paged - 1);
        }
        $eventsList = $eventManager->getEvents($postPerPage, $offset);
        $total = ceil(($eventManager->getNumberOfAllLiveAndFutureEvents() / $postPerPage));

        $html = "";
        $html .= "<div class='events-list'>";
        foreach ($eventsList as $event) {
            $startDate = new DateTime($event->getEventStartDate()->format('Y-m-d H:i:s'));
            $endDate = new DateTime($event->getEventEndDate()->format('Y-m-d H:i:s'));
            $image = $event->getImage();
            $html .= "<div class='event'>";
            $html .= "<h2 class='title'>" . $event->getTitle() . "</h2>";
            $html .= "<div class='columns'>";
            $html .= "<div class='column'>";
            if ('' !== $image) {
                $html .= "<div class='event-image'><img src=" . $image . " width='200px' height='200px'></div>";
            } else {
                $html .= "<div class='event-image'><img src=" . WS_EVENT_CALENDAR_PLUGIN_DIR_URL . '/assets/src/img/event-placeholder.png' . " width='200px' height='200px'></div>";
            }
            $html .= "</div>";
            $html .= "<div class='column'>";
            $html .= "<div class='event-info'>";
            $html .= "<h3 class='sub-title'>" . $event->getSubTitle() . "</h3>";
            $html .= "<p class='start-date'>" . __('Start: ', 'web-systems-events-calendar') . $startDate->format('Y-m-d H:i') . "</p>";
            $html .= "<p class='end-date'>" . __('End: ', 'web-systems-events-calendar') . $endDate->format('Y-m-d H:i') . "</p>";
            $html .= "<p class='time-left'></p>";
            $html .= "<p class='description'>" . $event->getDescription() . "</p>";
            $html .= "</div>";
            $html .= "<a class='btn btn-primary btn-lg show-more-button' href=" . get_permalink($event->getID()) . ">" . __('Show more', 'web-systems-events-calendar') . "</a>";
            $html .= "</div>";
            $html .= "</div>";
            $html .= "</div>";
        }
        $html .= "</div>";

        if ($pagination && is_int($postPerPage) && $postPerPage > 1) {
            $html .= "<div class='pagination'>";
            $big = 999999999;
            $html .= paginate_links(array(
                'base' => str_replace($big, '%#%', get_pagenum_link($big)),
                'format' => '?paged=%#%',
                'current' => max(1, get_query_var('paged')),
                'total' => $total,
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;'
            ));
            $html .= "</div>";
        }
        return $html;
    }
    public function displaySingleEvent($event)
    {
    }
}
