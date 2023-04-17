<?php

namespace Jakubciszak\GenericAvailability;

use Munus\Collection\GenericList;

trait EventsAwareTrait
{
    private GenericList $events;

    private function registerEvent(AvailabilityEvent $event): void
    {
        $this->events = $this->events->prepend($event);
    }

    public function popEvent(): AvailabilityEvent
    {
        $event = $this->events->get();
        $this->events = $this->events->take($this->events->count(fn() => true) - 1);
        return $event;
    }
}
