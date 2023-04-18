<?php

use Jakubciszak\GenericAvailability\Common\TheId;
use Jakubciszak\GenericAvailability\Event\ReservationCancelled;
use Jakubciszak\GenericAvailability\Period;
use Jakubciszak\GenericAvailability\Policy;
use Jakubciszak\GenericAvailability\ReservationRequest;
use Jakubciszak\GenericAvailability\Resource;
use Munus\Collection\GenericList;
use PHPUnit\Framework\TestCase;

class ReservationTest extends TestCase
{
    /**
     * @var Policy
     */
    private $allOrNothingPolicy;

    public function setUp(): void
    {
        parent::setUp();
        $this->allOrNothingPolicy = new class() implements Policy {
            public function isSatisfiedBy(ReservationRequest $reservationRequest): bool
            {
                return $reservationRequest->requestedResources->equals($reservationRequest->reservedResources());
            }
        };
    }

    public function testCreateEmptyReservation(): void
    {
        $reservationRequest = new ReservationRequest(
                new Period(new DateTimeImmutable('2022-02-01'), new DateTimeImmutable('2022-03-01')),
                GenericList::empty()
        );
        $reservation = $reservationRequest->reserve();
        self::assertTrue($reservation->reservedResources->isEmpty());
    }

    public function testCreateReservationWithResources(): void
    {
        $reservationRequest = new ReservationRequest(
                new Period(new DateTimeImmutable('2022-02-01'), new DateTimeImmutable('2022-03-01')),
                GenericList::of(
                        new Resource(TheId::generate()),
                        new Resource(TheId::generate()),
                        new Resource(TheId::generate()),
                )
        );
        $reservation = $reservationRequest->reserve();
        self::assertFalse($reservation->reservedResources->isEmpty());
        self::assertCount(3, $reservation->reservedResources);
        self::assertContainsOnlyInstancesOf(Resource::class, $reservation->reservedResources);
    }

    public function testCancelReservation(): void
    {
        $reservationRequest = new ReservationRequest(
                new Period(new DateTimeImmutable('2022-02-01'), new DateTimeImmutable('2022-03-01')),
                GenericList::of(
                        new Resource(TheId::generate()),
                        new Resource(TheId::generate()),
                        new Resource(TheId::generate()),
                )
        );
        $reservation = $reservationRequest->reserve();
        $reservation->cancel();

        self::assertTrue($reservation->isCancelled());
        $reservation->reservedResources->forEach(
                fn(Resource $resource) => self::assertFalse($resource->isReservedOn($reservation->period))
        );
        $event = $reservation->popEvent();
        self::assertInstanceOf(ReservationCancelled::class, $event);
    }

    public function testReservedSuccessWithPolicySucceed(): void
    {
        $resource1 = new Resource(TheId::generate());
        $resource2 = new Resource(TheId::generate());
        $resource3 = new Resource(TheId::generate());
        $period = new Period(new DateTimeImmutable('2022-02-01'), new DateTimeImmutable('2022-03-01'));
        $reservationRequest = new ReservationRequest(
                $period,
                GenericList::of($resource1, $resource2, $resource3),
                GenericList::of($this->allOrNothingPolicy)
        );
        $reservation = $reservationRequest->reserve();
        self::assertNotNull($reservation);
        self::assertFalse($reservationRequest->hasErrors());
        self::assertEquals($period, $reservation->period);
        self::assertEquals(3, $reservation->reservedResources->count(fn() => true));
        self::assertEquals($reservationRequest->requestedResources, $reservation->reservedResources);
    }

    public function testReservedFailedWithPolicyFail(): void
    {
        $resource1 = new Resource(TheId::generate());
        $resource2 = new Resource(TheId::generate());
        $resource3 = new Resource(TheId::generate());
        $period = new Period(new DateTimeImmutable('2022-02-01'), new DateTimeImmutable('2022-03-01'));
        $resource3->reserve($period);
        $reservationRequest = new ReservationRequest(
                $period,
                GenericList::of($resource1, $resource2, $resource3),
                GenericList::of($this->allOrNothingPolicy)
        );
        $reservation = $reservationRequest->reserve();
        self::assertNull($reservation);
        self::assertTrue($reservationRequest->hasErrors());
        self::assertEquals(1, $reservationRequest->errors()->count(fn() => true));
    }

    public function testReserveNotAllResources(): void
    {
        $resource1 = new Resource(TheId::generate());
        $resource2 = new Resource(TheId::generate());
        $resource3 = new Resource(TheId::generate());
        $period = new Period(new DateTimeImmutable('2022-02-01'), new DateTimeImmutable('2022-03-01'));
        $resource3->reserve($period);
        $reservationRequest = new ReservationRequest(
                $period,
                GenericList::of($resource1, $resource2, $resource3),
                GenericList::empty()
        );
        $reservation = $reservationRequest->reserve();
        self::assertNotNull($reservation);
        self::assertTrue($reservationRequest->hasErrors());
        self::assertEquals($period, $reservation->period);
        self::assertEquals(2, $reservation->reservedResources->count(fn() => true));
        self::assertNotEquals($reservationRequest->requestedResources, $reservation->reservedResources);
    }
}
