<?php

namespace Admin\Tests\Feature;

use Admin\Tests\TestCase;
use Laravel\Dusk\Browser;

class DashboardTest extends TestCase
{
    public function test_load_dashboard(): void
    {
        $this->adminAuth();

        $this->browse(function (Browser $browser) {

            $browser->visitRoute('admin.dashboard')
                ->assertSee('Dashboard');

            $browser->waitFor('#chart0');
            $browser->waitFor('#chart1');
            $browser->waitFor('#chart2');

            $browser->assertScript("document.querySelector('#chart0') !== null");
            $browser->assertScript("document.querySelector('#chart1') !== null");
            $browser->assertScript("document.querySelector('#chart2') !== null");
        });
    }
}
