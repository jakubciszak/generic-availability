<?php

namespace Jakubciszak\GenericAvailability\Common;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Stringable;

readonly class TheId implements Stringable
{
    private function __construct(public UuidInterface $uuid)
    {
    }

    public static function generate(): TheId
    {
        return new self(Uuid::uuid4());
    }

    public function __toString(): string
    {
        return $this->uuid->toString();
    }
}
