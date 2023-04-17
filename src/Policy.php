<?php

namespace Jakubciszak\GenericAvailability;

interface Policy
{
    public function isSatisfiedBy(ReservationRequest $reservationRequest): bool;
}
