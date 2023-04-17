<?php

namespace Jakubciszak\GenericAvailability;

use DateTimeImmutable;

readonly class Period
{
    public function __construct(public DateTimeImmutable $from, public DateTimeImmutable $to)
    {
    }

    public function overlaps(Period $period): bool
    {
        return ($this->to <= $period->to && $this->from >= $period->from);
    }

    public function equals(Period $period, PeriodPrecision $precision = PeriodPrecision::DAY): bool
    {
        return $this->from->format($precision->value) === $period->from->format($precision->value)
            && $this->to->format($precision->value) === $period->to->format($precision->value);
    }
}
