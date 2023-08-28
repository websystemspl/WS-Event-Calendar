<?php

namespace WsEventCalendar\App\Events;

use DateTime;

class Event
{
    private int $id;
    private string $title;
    private string $image;
    private ?string $subTitle;
    private ?string $description;
    private DateTime $eventStartDate;
    private DateTime $eventEndDate;
    private ?string $link;

    public function getID()
    {
        return $this->id;
    }
    public function getTitle(): string
    {
        return $this->title;
    }
    public function getImage(): string
    {
        return $this->image;
    }
    public function getSubTitle(): ?string
    {
        return $this->subTitle;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function getEventStartDate(): DateTime
    {
        return $this->eventStartDate;
    }
    public function getEventEndDate(): DateTime
    {
        return $this->eventEndDate;
    }
    public function getLLink(): ?string
    {
        return $this->link;
    }
    public function __construct($id, string $title, string $image, ?string $subTitle, ?string $description, DateTime $eventStartDate, DateTime $eventEndDate, ?string $link)
    {
        $this->id = $id;
        $this->title = $title;
        $this->image = $image;
        $this->subTitle = $subTitle;
        $this->description = $description;
        $this->eventStartDate = $eventStartDate;
        $this->eventEndDate = $eventEndDate;
        $this->link = $link;
    }
}
