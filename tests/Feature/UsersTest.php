<?php

namespace Admin\Tests\Feature;

use Admin\Tests\TestCase;
use Laravel\Dusk\Browser;

class UsersTest extends TestCase
{
    public function test_change_admin_password(): void
    {
        $this->adminAuth();

        $this->browse(function (Browser $browser) {
            $browser->visitRoute('admin.profile')
                ->assertSee('Admin')
                ->type('password', 'admin2')
                ->type('password_confirmation', 'admin2')
                ->click('[data-click="submit"]')
                //->assertSee('Saved successfully!');
                ->screenshot('test_change_admin_password');
        });

        $this->browse(function (Browser $browser) {

            if($browser->element('[data-original-title="Logout"]')) {
                $browser->visitRoute('admin.profile.logout');
            }

            $browser->visitRoute('admin.login')
                ->type('login', 'admin')
                ->type('password', 'admin2')
                ->press('Sign In')
                ->assertSee('admin@admin.com');
        });

        $this->browse(function (Browser $browser) {

            $browser->visitRoute('admin.profile')
                ->assertSee('Admin')
                ->type('password', 'admin')
                ->type('password_confirmation', 'admin')
                ->click('button[data-click="submit"]');
        });
    }
}
