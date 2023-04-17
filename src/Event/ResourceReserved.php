<?php

namespace Jakubciszak\GenericAvailability\Event;

use Jakubciszak\GenericAvailability\AvailabilityEvent;
use Jakubciszak\GenericAvailability\Resource;

readonly class ResourceReserved implements AvailabilityEvent
{
    public function __construct(public Resource $resource)
    {
    }
}
