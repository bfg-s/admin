<?php

namespace LteAdmin\Tests;

use Tests\TestCase;

class LteTest extends TestCase
{
    public function test_has_guard()
    {
        $this->assertIsArray(
            config("auth.guards.lte")
        );
    }
}
