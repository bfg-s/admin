<?php

namespace Admin\Tests;

use Admin\Tests\Traits\DatabaseMigrations;
use Admin\Tests\Traits\SetUp;
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
