<?php

namespace Common;

use Jakubciszak\GenericAvailability\Common\TheId;
use PHPUnit\Framework\TestCase;

class TheIdTest extends TestCase
{

    public function testGenerate(): void
    {
        $id1 = TheId::generate();
        $id2 = TheId::generate();

        $this->assertNotEquals($id1, $id2);
    }

}
