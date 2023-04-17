<?php

namespace Jakubciszak\GenericAvailability\Event;

use Jakubciszak\GenericAvailability\AvailabilityEvent;
use Jakubciszak\GenericAvailability\Reservation;

readonly class ReservationCancelled implements AvailabilityEvent
{
    public function __construct(public Reservation $reservation)
    {
    }
}
