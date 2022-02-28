<?php

namespace LteAdmin\Tests;

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
            $browser->visitRoute('lte.login')
                ->type('login', 'root')
                ->type('password', 'root')
                ->press(__('lte.sign_in'))
                ->assertRouteIs('lte.dashboard')
                ->visitRoute('lte.profile.logout');

            $browser->visitRoute('lte.login')
                ->type('login', 'admin')
                ->type('password', 'admin')
                ->press(__('lte.sign_in'))
                ->assertRouteIs('lte.dashboard')
                ->visitRoute('lte.profile.logout');

            $browser->visitRoute('lte.login')
                ->type('login', 'moderator')
                ->type('password', 'moderator')
                ->press(__('lte.sign_in'))
                ->assertRouteIs('lte.dashboard')
                ->visitRoute('lte.profile.logout');
        });
    }

    /**
     * @return void
     * @throws Throwable
     */
    public function test_lte_fail_auth()
    {
        $this->browse(function (Browser $browser) {
            $browser->visitRoute('lte.login')
                ->type('login', 'fail-test')
                ->type('password', 'fail-test')
                ->press(__('lte.sign_in'))
                ->assertRouteIs('lte.login');
        });
    }
}
