<?php

namespace Admin\Tests\Unit;

use Admin\Tests\TestCase;

class SystemTest extends TestCase
{
    public function test_has_guard()
    {
        $this->assertIsArray(
            config("auth.guards.admin")
        );
    }

    public function test_has_system_disk()
    {
        $this->assertIsArray(
            config("filesystems.disks.admin")
        );
    }
}
