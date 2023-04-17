# Availability Domain

This domain is responsible for managing the availability of resources and reservations. The domain contains the
necessary classes and interfaces for checking availability, creating and canceling reservations, and managing resources.

## Directory Structure

```
src
├── AvailabilityEvent.php
├── Common
│ └── TheId.php
├── Event
│ ├── ReservationCancelled.php
│ ├── ReservationCreated.php
│ └── ResourceAddedToReservation.php
├── Exception
│ └── PeriodsOverlapsException.php
├── Period.php
├── PeriodPrecision.php
├── Policy
├── Policy.php
├── Reservation.php
├── ReservationRequest.php
└── Resource.php
```

## Class and Interface descriptions

* `AvailabilityEvent.php`: Interface for all events related to the Availability domain.
* `Common/TheId.php`: Class representing unique identifiers for various objects within the domain.
* `Event/ReservationCancelled.php`: Class representing a reservation cancellation event.
* `Event/ReservationCreated.php`: Class representing a reservation creation event.
* `Event/ResourceAddedToReservation.php`: Class representing an event of adding a resource to a reservation.
* `Exception/PeriodsOverlapsException.php`: Exception thrown when reservation periods overlap.
* `Period.php`: Class representing a time period for which a resource can be reserved.
* `PeriodPrecision.php`: Class representing the precision for comparing time periods.
* `Policy.php`: Interface for various policies related to reservations and availability.
* `Reservation.php`: Class representing a resource reservation.
* `ReservationRequest.php`: Class representing a resource reservation request.
* `Resource.php`: Class representing a resource that can be reserved.

## Usage Example

The Availability domain can be used in various applications such as hotel reservation systems, car rental services, or
event planning. Thanks to the classes and interfaces included in this domain, it is easy to check resource availability,
create and cancel reservations, and manage resources and their availability according to various business policies.

```php
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
```
