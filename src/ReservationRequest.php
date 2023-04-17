<?php

namespace Jakubciszak\GenericAvailability;

use Jakubciszak\GenericAvailability\Common\TheId;
use Jakubciszak\GenericAvailability\Event\ReservationRequested;
use Jakubciszak\GenericAvailability\Event\ResourceReserved;
use Jakubciszak\GenericAvailability\Exception\PeriodsOverlapsException;
use Munus\Collection\GenericList;

class ReservationRequest
{
    use EventsAwareTrait;

    /**
     * @var GenericList<string>
     */
    private GenericList $errors;
    /**
     * @var GenericList<Resource>
     */
    private GenericList $reservedResources;

    /**
     * @param Period $period
     * @param GenericList<Resource> $requestedResources
     * @param GenericList<Policy> $policies
     */
    public function __construct(
        public readonly Period $period,
        public readonly GenericList $requestedResources,
        public readonly GenericList $policies
    ) {
        $this->reservedResources = GenericList::empty();
        $this->errors = GenericList::empty();
        $this->registerEvent(new ReservationRequested($this));
    }

    public function hasErrors(): bool
    {
        return !$this->errors->isEmpty();
    }

    private function tryToReserveResource(Resource $resource): bool
    {
        try {
            $resource->reserve($this->period);
            return true;
        } catch (PeriodsOverlapsException) {
            $this->addError(sprintf('Resource %s is not available.', $resource->resourceId));
            return false;
        }
    }

    public function reserve(): ?Reservation
    {
        $this->reservedResources = $this->requestedResources->filter(
            fn(Resource $resource) => $this->tryToReserveResource($resource)
        );
        $satisfiedPolicies = $this->policies->filter(fn(Policy $policy) => $policy->isSatisfiedBy($this));
        if (!$this->reservedResources->isEmpty()
            && $this->policies->equals($satisfiedPolicies)
        ) {
            $this->reservedResources->forEach(
                fn(Resource $resource) => $this->registerEvent(new ResourceReserved($resource))
            );
            return new Reservation(
                TheId::generate(),
                $this->period,
                $this->reservedResources
            );
        }
        return null;
    }

    public function addError(string $error): void
    {
        $this->errors = $this->errors->append($error);
    }

    public function errors(): GenericList
    {
        return $this->errors;
    }

    public function reservedResources(): GenericList
    {
        return $this->reservedResources;
    }
}
