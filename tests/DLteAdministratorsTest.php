<?php

namespace LteAdmin\Tests;

use Laravel\Dusk\Browser;
use Throwable;

class DLteAdministratorsTest extends DuskTestCase
{
    /**
     * @return void
     * @throws Throwable
     */
    public function test_lte_go_to_administrator_list()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(admin()->find(1), config('lte.auth.guards.lte.provider'))
                ->visitRoute('lte.admin.lte_user.index')
                //->press(__('lte.sign_in'))
                ->assertRouteIs('lte.admin.lte_user.index');
            //->visitRoute('lte.profile.logout');

//            $browser->visitRoute('lte.login')
//                ->type('login', 'admin')
//                ->type('password', 'admin')
//                ->press(__('lte.sign_in'))
//                ->assertRouteIs('lte.dashboard')
//                ->visitRoute('lte.profile.logout');
//
//            $browser->visitRoute('lte.login')
//                ->type('login', 'moderator')
//                ->type('password', 'moderator')
//                ->press(__('lte.sign_in'))
//                ->assertRouteIs('lte.dashboard')
//                ->visitRoute('lte.profile.logout');
        });
    }
}
