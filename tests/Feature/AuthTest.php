<?php

namespace Admin\Tests\Feature;

use Admin\Tests\TestCase;
use Laravel\Dusk\Browser;

class AuthTest extends TestCase
{
    public function test_can_login(): void
    {
        $this->rootAuth();
    }

    public function test_can_go_to_dashboard_from_home()
    {
        $this->rootAuth();

        $this->browse(function (Browser $browser) {
            $browser->visitRoute('admin.home')
                ->assertPathIs('/en/bfg/dashboard');
        });
    }

    public function test_open_dashboard()
    {
        $this->rootAuth();

        $this->browse(function (Browser $browser) {

            $browser->visitRoute('admin.dashboard')
                ->assertSee('Dashboard');
        });
    }

    public function test_admin_access()
    {
        $this->adminAuth();
    }

    public function test_moderator_access()
    {
        $this->moderatorAuth();
    }
}
