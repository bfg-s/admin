<?php

namespace Admin\Tests\Feature;

use Admin\Tests\TestCase;
use Laravel\Dusk\Browser;

class AccessTest extends TestCase
{
    public function test_moderator_access_denied(): void
    {
        $this->moderatorAuth();

        $this->browse(function (Browser $browser) {
            $browser->visitRoute('admin.administration.admin_user.index')
                ->assertPathIs('/en/bfg/administration/admin_user')
                ->assertSee('Access denied!');
        });
    }

    public function test_moderator_access_denied_hide_menu(): void
    {
        $this->moderatorAuth();

        $this->browse(function (Browser $browser) {
            $browser->visitRoute('admin.dashboard')
                ->assertSee('Dashboard')
                ->assertDontSee('Administration')
                ->assertDontSee('Roles')
                ->assertDontSee('Permission');
        });
    }
}
