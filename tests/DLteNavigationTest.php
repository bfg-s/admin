<?php

namespace Admin\Tests;

use Laravel\Dusk\Browser;
use Throwable;

class DLteNavigationTest extends DuskTestCase
{
    /**
     * @return void
     * @throws Throwable
     */
    public function test_lte_go_to_custom_user_controller()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(admin()->find(1), config('admin.auth.guards.admin.provider'))
                ->visit('admin/users')
                ->assertPathIs(admin_uri('users'))
                ->assertSee('Users')
                ->assertSee(__('admin.list'))
                ->visit(admin_uri('users/create'))
                ->assertPathIs(admin_uri('users/create'))
                ->assertSee(__('admin.add'));
        });
    }


}
