<?php

namespace Admin\Tests;

use Laravel\Dusk\Browser;
use Throwable;

class DAdministratorsTest extends DuskTestCase
{
    /**
     * @return void
     * @throws Throwable
     */
    public function test_lte_go_to_administrator_list()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(admin()->find(1), config('admin.auth.guards.admin.provider'))
                ->visitRoute('admin.admin.lte_user.index')
                //->press(__('admin.sign_in'))
                ->assertRouteIs('admin.admin.lte_user.index');
            //->visitRoute('admin.profile.logout');

//            $browser->visitRoute('admin.login')
//                ->type('login', 'admin')
//                ->type('password', 'admin')
//                ->press(__('admin.sign_in'))
//                ->assertRouteIs('admin.dashboard')
//                ->visitRoute('admin.profile.logout');
//
//            $browser->visitRoute('admin.login')
//                ->type('login', 'moderator')
//                ->type('password', 'moderator')
//                ->press(__('admin.sign_in'))
//                ->assertRouteIs('admin.dashboard')
//                ->visitRoute('admin.profile.logout');
        });
    }
}
