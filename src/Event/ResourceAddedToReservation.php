<?php

namespace Jakubciszak\GenericAvailability\Event;

use Jakubciszak\GenericAvailability\AvailabilityEvent;
use Jakubciszak\GenericAvailability\Common\TheId;

readonly class ResourceAddedToReservation implements AvailabilityEvent
{
    public function __construct(public TheId $reservationId, public TheId $resourceId)
    {
    }
}
