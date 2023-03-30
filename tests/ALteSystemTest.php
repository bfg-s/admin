<?php

namespace Admin\Tests;

class ALteSystemTest extends TestCase
{
    public function test_has_guard()
    {
        $this->assertIsArray(
            config("auth.guards.admin")
        );
    }
}
