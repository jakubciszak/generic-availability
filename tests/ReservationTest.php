<?php

use Jakubciszak\GenericAvailability\Common\TheId;
use Jakubciszak\GenericAvailability\Event\ReservationCancelled;
use Jakubciszak\GenericAvailability\Period;
use Jakubciszak\GenericAvailability\Reservation;
use Jakubciszak\GenericAvailability\Resource;
use Munus\Collection\GenericList;
use PHPUnit\Framework\TestCase;

class ReservationTest extends TestCase
{
    public function testCreateEmptyReservation(): void
    {
        $reservation = new Reservation(
            TheId::generate(),
            new Period(new DateTimeImmutable('2022-02-01'), new DateTimeImmutable('2022-03-01')),
            GenericList::empty()
        );
        self::assertTrue($reservation->reservedResources->isEmpty());
    }

    public function testCreateReservationWithResources(): void
    {
        $reservation = new Reservation(
            TheId::generate(),
            new Period(new DateTimeImmutable('2022-02-01'), new DateTimeImmutable('2022-03-01')),
            GenericList::of(
                new Resource(TheId::generate()),
                new Resource(TheId::generate()),
                new Resource(TheId::generate()),
            )
        );
        self::assertFalse($reservation->reservedResources->isEmpty());
        self::assertCount(3, $reservation->reservedResources);
        self::assertContainsOnlyInstancesOf(Resource::class, $reservation->reservedResources);
    }

    public function testCancelReservation(): void
    {
        $reservation = new Reservation(
            TheId::generate(),
            new Period(new DateTimeImmutable('2022-02-01'), new DateTimeImmutable('2022-03-01')),
            GenericList::of(
                new Resource(TheId::generate()),
                new Resource(TheId::generate()),
                new Resource(TheId::generate()),
            )
        );
        $reservation->cancel();
        self::assertTrue($reservation->isCancelled());
        $reservation->reservedResources->forEach(
            fn(Resource $resource) => self::assertFalse($resource->isReservedOn($reservation->period))
        );
        $event = $reservation->popEvent();
        self::assertInstanceOf(ReservationCancelled::class, $event);
    }
}
