<?php

namespace WsEventCalendar\App\Events;

use DateTime;

class Events{

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

    public function displayEventList($eventsList){
        $html = "";
        $html .= "<div class='events-list'>";
        foreach($eventsList as $event){
            $startDate = new DateTime($event->getEventStartDate()->format('Y-m-d H:i:s'));
            $endDate = new DateTime($event->getEventEndDate()->format('Y-m-d H:i:s'));
            $image = $event->getImage();
            
            $html .= "<div class='event'>";
            $html .= "<div class='title'>" . $event->getTitle() . "</div>";
            $html .= "<div class='column'>";
            if('' !== $image){
                $html .= "<div class='event-image'><img src=". $image ." width='150px' height='150px'></div>";
            }
            $html .= "</div>";
            $html .= "<div class='column'>";
            $html .= "<p class='sub-title'>" . $event->getSubTitle() . "</p>";
            $html .= "<p class='start-date'>" . $startDate->format('Y-m-d H:i') . "</p>";
            $html .= "<p class='end-date'>" . $endDate->format('Y-m-d H:i') . "</p>";
            $html .= "<p class='time-left'></p>";
            $html .= "</div>";
            $html .= "<div class='event-info'>";
            $html .= "</div>";
            $html .= "</div>";
        }
        $html .= "</div>";
        return $html;
    }
    public function displaySingleEvent($event){

    }
}