<?php

namespace LteAdmin\Tests;

class ALteSystemTest extends TestCase
{
    public function test_has_guard()
    {
        $this->assertIsArray(
            config("auth.guards.lte")
        );
    }
}
