<?php

namespace Admin\Tests;

use Admin\Models\AdminSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

abstract class TestCase extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Artisan::call('db:seed', ['--class' => AdminSeeder::class]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function rootAuth()
    {
        $this->browse(function (Browser $browser) {

            if($browser->element('[data-original-title="Logout"]')) {
                $browser->visitRoute('admin.profile.logout');
            }

            $browser->visitRoute('admin.login')
                ->type('login', 'root')
                ->type('password', 'root')
                ->press('Sign In')
                ->assertSee('root@root.com');
        });
    }

    public function adminAuth()
    {
        $this->browse(function (Browser $browser) {

            if($browser->element('[data-original-title="Logout"]')) {
                $browser->visitRoute('admin.profile.logout');
            }

            $browser->visitRoute('admin.login')
                ->type('login', 'admin')
                ->type('password', 'admin')
                ->press('Sign In')
                ->assertSee('admin@admin.com');
        });
    }

    public function moderatorAuth()
    {
        $this->browse(function (Browser $browser) {

            if($browser->element('[data-original-title="Logout"]')) {
                $browser->visitRoute('admin.profile.logout');
            }

            $browser->visitRoute('admin.login')
                ->type('login', 'moderator')
                ->type('password', 'moderator')
                ->press('Sign In')
                ->assertSee('moderator@moderator.com');
        });
    }
}
