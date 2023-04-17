<?php


use Jakubciszak\GenericAvailability\Common\TheId;
use Jakubciszak\GenericAvailability\Exception\PeriodsOverlapsException;
use Jakubciszak\GenericAvailability\Period;
use Jakubciszak\GenericAvailability\Resource;
use PHPUnit\Framework\TestCase;

class ResourceTest extends TestCase
{
    public function testReserveResource(): void
    {
        $resource = new Resource(TheId::generate());
        $period = new Period(
            new DateTimeImmutable('2020-01-01'),
            new DateTimeImmutable('2020-01-02')
        );
        $resource->reserve($period);

        $reserved = $resource->reservedPeriods()
            ->filter(fn(Period $reservedPeriod) => $reservedPeriod->equals($period));
        $this->assertFalse($reserved->isEmpty());
    }

    public function testCanNotReserveResourceWhenPeriodsAreOverlapping(): void
    {
        $resource = new Resource(TheId::generate());
        $period = new Period(
            new DateTimeImmutable('2020-01-01'),
            new DateTimeImmutable('2020-01-02')
        );
        $resource->reserve($period);

        $this->expectException(PeriodsOverlapsException::class);
        $resource->reserve($period);
    }

    public function testCancelReservation(): void
    {
        $resource = new Resource(TheId::generate());
        $period = new Period(
            new DateTimeImmutable('2020-01-01'),
            new DateTimeImmutable('2020-01-02')
        );
        $resource->reserve($period);
        $reserved = $resource->reservedPeriods()
            ->filter(fn(Period $reservedPeriod) => $reservedPeriod->equals($period));
        $this->assertFalse($reserved->isEmpty());

        $resource->cancelReservation($period);

        $reserved = $resource->reservedPeriods()
            ->filter(fn(Period $reservedPeriod) => $reservedPeriod->equals($period));
        $this->assertTrue($reserved->isEmpty());
    }
}
