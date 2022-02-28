<?php

namespace LteAdmin\Tests;

use LteAdmin\Tests\Traits\DatabaseMigrations;
use LteAdmin\Tests\Traits\SetUp;
use Tests\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use DatabaseMigrations;
    use SetUp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpAdmin();
    }
}
