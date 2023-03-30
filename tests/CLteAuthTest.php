<?php

namespace Admin\Tests;

use Laravel\Dusk\Browser;
use Throwable;

class CLteAuthTest extends DuskTestCase
{
    /**
     * @return void
     * @throws Throwable
     */
    public function test_lte_auth()
    {
        $this->browse(function (Browser $browser) {
            $browser->visitRoute('admin.login')
                ->type('login', 'root')
                ->type('password', 'root')
                ->press(__('admin.sign_in'))
                ->assertRouteIs('admin.dashboard')
                ->visitRoute('admin.profile.logout');

            $browser->visitRoute('admin.login')
                ->type('login', 'admin')
                ->type('password', 'admin')
                ->press(__('admin.sign_in'))
                ->assertRouteIs('admin.dashboard')
                ->visitRoute('admin.profile.logout');

            $browser->visitRoute('admin.login')
                ->type('login', 'moderator')
                ->type('password', 'moderator')
                ->press(__('admin.sign_in'))
                ->assertRouteIs('admin.dashboard')
                ->visitRoute('admin.profile.logout');
        });
    }

    /**
     * @return void
     * @throws Throwable
     */
    public function test_lte_fail_auth()
    {
        $this->browse(function (Browser $browser) {
            $browser->visitRoute('admin.login')
                ->type('login', 'fail-test')
                ->type('password', 'fail-test')
                ->press(__('admin.sign_in'))
                ->assertRouteIs('admin.login');
        });
    }
}
