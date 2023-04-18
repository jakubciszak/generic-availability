<?php

namespace Jakubciszak\GenericAvailability;

use Jakubciszak\GenericAvailability\Common\TheId;
use Jakubciszak\GenericAvailability\Exception\PeriodsOverlapsException;
use Munus\Collection\GenericList;

class Resource
{
    /**
     * @var GenericList<Period>
     */
    private GenericList $reservedPeriods;

    public function __construct(
            public readonly TheId $resourceId,
    ) {
        $this->reservedPeriods = GenericList::empty();
    }

    /**
     * @throws PeriodsOverlapsException
     */
    public function reserve(Period $period): void
    {
        if (!$this->isAvailableOn($period)) {
            throw new PeriodsOverlapsException('Periods are overlapping.');
        }
        $this->reservedPeriods = $this->reservedPeriods->append($period);
    }

    public function cancelReservation(Period $period): void
    {
        $this->reservedPeriods = $this->reservedPeriods->filter(fn(Period $item) => !$item->equals($period));
    }

    public function isReservedOn(Period $period): bool
    {
        return !$this->reservedPeriods->filter(fn(Period $item) => $item->equals($period))->isEmpty();
    }

    public function isAvailableOn(Period $period): bool
    {
        return $this->reservedPeriods->filter(fn(Period $item) => $item->overlaps($period))->isEmpty();
    }

    public function reservedPeriods(): GenericList
    {
        return $this->reservedPeriods;
    }
}
