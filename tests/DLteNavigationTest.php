<?php

namespace LteAdmin\Tests;

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
            $browser->loginAs(admin()->find(1), config('lte.auth.guards.lte.provider'))
                ->visit('lte/users')
                ->assertPathIs(lte_uri('users'))
                ->assertSee('Users')
                ->assertSee(__('lte.list'))
                ->visit(lte_uri('users/create'))
                ->assertPathIs(lte_uri('users/create'))
                ->assertSee(__('lte.add'));
        });
    }


}
