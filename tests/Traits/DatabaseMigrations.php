<?php

namespace Admin\Tests\Traits;

use CreateTestTables;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Admin\Models\AdminSeeder;

trait DatabaseMigrations
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations {
        runDatabaseMigrations as baseRunDatabaseMigrations;
    }

    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @return void
     */
    public function runDatabaseMigrations()
    {
        $this->baseRunDatabaseMigrations();
        $this->artisan('migrate', [
            '--realpath' => __DIR__.'/../migrations'
        ]);
        $this->migrateTestTables();
        $this->artisan('db:seed', [
            'class' => AdminSeeder::class
        ]);
    }

    /**
     * run package database migrations.
     *
     * @return void
     * @throws FileNotFoundException
     */
    public function migrateTestTables()
    {
        $fileSystem = new Filesystem();

        $fileSystem->requireOnce(__DIR__.'/../migrations/2022_02_23_000000_create_test_tables.php');

        (new CreateTestTables())->up();
    }
}
