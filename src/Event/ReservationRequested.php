<?php

namespace Jakubciszak\GenericAvailability\Event;

use Jakubciszak\GenericAvailability\AvailabilityEvent;
use Jakubciszak\GenericAvailability\ReservationRequest;

readonly class ReservationRequested implements AvailabilityEvent
{
    public function __construct(public ReservationRequest $param)
    {
    }
}
