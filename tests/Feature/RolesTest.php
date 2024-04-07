<?php

namespace Admin\Tests\Feature;

use Admin\Tests\TestCase;
use Laravel\Dusk\Browser;

class RolesTest extends TestCase
{
    public function test_role_list_and_editables(): void
    {
        $this->adminAuth();

        $this->browse(function (Browser $browser) {

            $browser->visitRoute('admin.administration.admin_role.index');

            $browser->script('document.querySelector(\'.editable-click\').click()');

            $browser->pause(200);

            $browser->script("$('.editable-input input').val('NextModerator')");

            $browser->pause(200);

            $browser->script('document.querySelector(\'.editable-submit\').click()');

            $browser->waitFor('.swal2-success-ring')
                ->assertSee('Saved successfully!');

            $browser->assertSee('NextModerator');



            $browser->script('document.querySelector(\'.editable-click\').click()');

            $browser->pause(200);

            $browser->script("$('.editable-input input').val('Moderator')");

            $browser->pause(200);

            $browser->script('document.querySelector(\'.editable-submit\').click()');

            $browser->waitFor('.swal2-success-ring')
                ->assertSee('Saved successfully!');

            $browser->assertSee('Moderator');
        });
    }

    public function test_edit_moderator_role()
    {
        $this->adminAuth();

        $this->browse(function (Browser $browser) {

            $browser->visitRoute('admin.administration.admin_role.edit', 3);

            $browser->type('name', 'PowerModerator');

            $browser->script('document.querySelector(\'[data-click="submit"]\').click()');

            $browser->waitFor('.swal2-success-ring')
                ->assertSee('Saved successfully!');

            $browser->assertSee('PowerModerator');


            $browser->visitRoute('admin.administration.admin_role.edit', 3);

            $browser->type('name', 'Moderator');

            $browser->script('document.querySelector(\'[data-click="submit"]\').click()');

            $browser->waitFor('.swal2-success-ring')
                ->assertSee('Saved successfully!');

            $browser->assertDontSee('PowerModerator');

            $browser->assertSee('Moderator');
        });
    }
}
