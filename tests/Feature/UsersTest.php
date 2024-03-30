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
                ->assertSee('Admin');
//                ->type('#input_password', 'admin2')
//                ->type('#input_password_confirmation', 'admin2')
//                ->click('button[data-click="submit"]')
//                ->assertSee('Saved successfully!');
        });

//        $this->browse(function (Browser $browser) {
//
//            if($browser->element('[data-original-title="Logout"]')) {
//                $browser->visitRoute('admin.profile.logout');
//            }
//
//            $browser->visitRoute('admin.login')
//                ->type('login', 'admin')
//                ->type('password', 'admin2')
//                ->press('Sign In')
//                ->assertSee('admin@admin.com');
//        });
//
//        $this->browse(function (Browser $browser) {
//
//            $browser->visitRoute('admin.profile')
//                ->type('#input_password', 'admin')
//                ->type('#input_password_confirmation', 'admin')
//                ->click('button[data-click="submit"]')
//                ->assertSee('Saved successfully!');
//        });
    }
}
