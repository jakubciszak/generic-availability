<?php

namespace Jakubciszak\GenericAvailability;

use Jakubciszak\GenericAvailability\Common\TheId;
use Jakubciszak\GenericAvailability\Event\ReservationCancelled;
use Jakubciszak\GenericAvailability\Event\ReservationCreated;
use Munus\Collection\GenericList;

class Reservation
{
    use EventsAwareTrait;

    private GenericList $events;
    private bool $cancelled = false;

    public function __construct(
        public readonly TheId $reservationId,
        public readonly Period $period,
        public readonly GenericList $reservedResources
    ) {
        $this->events = GenericList::empty();
        $this->registerEvent(new ReservationCreated($this));
    }

    public function cancel(): void
    {
        if ($this->isCancelled()) {
            return;
        }
        $this->reservedResources->forEach(fn(Resource $resource) => $resource->cancelReservation($this->period));
        $this->cancelled = true;
        $this->registerEvent(new ReservationCancelled($this));
    }

    public function isCancelled(): bool
    {
        return $this->cancelled;
    }
}
