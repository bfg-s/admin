<?php

namespace Admin\Tests\Feature;

use Admin\Tests\TestCase;
use Laravel\Dusk\Browser;

class UsersTest extends TestCase
{
    public function test_change_admin_email(): void
    {
        $this->adminAuth();

        $this->browse(function (Browser $browser) {

            $browser->visitRoute('admin.profile')
                ->assertSee('Admin')
                ->type('email', 'admin2@admin2.com');

            $browser->script('document.querySelector(\'[data-click="submit"]\').click()');

            $browser->waitFor('.swal2-success-ring')
                ->assertSee('Saved successfully!')
                ->assertSee('admin2@admin2.com');
        });

        $this->browse(function (Browser $browser) {

            $browser->visitRoute('admin.profile')
                ->assertSee('Admin')
                ->type('email', 'admin@admin.com');

            $browser->script('document.querySelector(\'[data-click="submit"]\').click()');

            $browser->waitFor('.swal2-success-ring')
                ->assertSee('Saved successfully!')
                ->assertSee('admin@admin.com');
        });
    }

    public function test_change_admin_password(): void
    {
        $this->adminAuth();

        $this->browse(function (Browser $browser) {

            $browser->visitRoute('admin.profile')
                ->assertSee('Admin')
                ->type('password', 'admin2')
                ->type('password_confirmation', 'admin2');

            $browser->script('document.querySelector(\'[data-click="submit"]\').click()');

            $browser->waitFor('.swal2-success-ring')
                ->assertSee('Saved successfully!');
        });

        $this->browse(function (Browser $browser) {

            if($browser->element('[data-original-title="Logout"]')) {
                $browser->visitRoute('admin.profile.logout');
            }

            $browser->visitRoute('admin.login')
                ->type('login', 'admin')
                ->type('password', 'admin2')
                ->press('Sign In')
                ->assertSee('admin@admin.com');
        });

        $this->browse(function (Browser $browser) {

            $browser->visitRoute('admin.profile')
                ->assertSee('Admin')
                ->type('password', 'admin')
                ->type('password_confirmation', 'admin');

            $browser->script('document.querySelector(\'[data-click="submit"]\').click()');

            $browser->waitFor('.swal2-success-ring')
                ->assertSee('Saved successfully!');
        });
    }

    public function test_2fa_modal()
    {
        $this->adminAuth();

        $this->browse(function (Browser $browser) {

            $browser->visitRoute('admin.profile')
                ->assertSee('Admin');

            $browser->script('document.querySelector(\'#tab-c2082d1fbb2e54f6185600d922e17677-1-label\').click()');
            $browser->pause(200);
            $browser->script('document.querySelector(\'[data-2fa-enable]\').click()');
            $browser->waitFor('#input_password');
            $browser->type('password', 'admin');
            $browser->script('document.querySelector(\'.btn-outline-success\').click()');
            $browser->waitFor('[data-2fa-qr]');
            $browser->assertSee('Generate a One Time Password (OTP) and enter the value below.');
        });
    }

    public function test_edit_administrators_list()
    {
        $this->adminAuth();

        $this->browse(function (Browser $browser) {

            $browser->visitRoute('admin.administration.admin_user.index')
                ->assertSee('Administrators');

            $browser->visitRoute('admin.administration.admin_user.edit', 3)
                ->assertSee('Edit: moderator');

            $browser->attach('avatar', public_path('vendor/admin-shopify/moderator.png'));
            $browser->pause(1000);
            $browser->type('name', 'Moderator 2');
            $browser->type('login', 'moderator2');

            $browser->script('document.querySelector(\'[data-click="submit"]\').click()');

            $browser->waitFor('.swal2-success-ring')
                ->assertSee('Saved successfully!')
                ->assertSee('Moderator 2');

            $browser->visitRoute('admin.administration.admin_user.edit', 3)
                ->assertSee('Edit: moderator2');

            $browser->attach('avatar', public_path('admin/img/user.jpg'));
            $browser->pause(1000);
            $browser->type('name', 'Moderator');
            $browser->type('login', 'moderator');

            $browser->script('document.querySelector(\'[data-click="submit"]\').click()');

            $browser->waitFor('.swal2-success-ring')
                ->assertSee('Saved successfully!')
                ->assertSee('Moderator');
        });
    }

    public function test_add_administrator()
    {
        $this->adminAuth();

        $this->browse(function (Browser $browser) {

            $browser->visitRoute('admin.administration.admin_user.create')
                ->assertSee('Add admin');
            $browser->type('login', 'new_admin');
            $browser->type('name', 'New admin');
            $browser->type('email', 'new_admin@admin.com');
            $browser->select('roles[]', [2]);

            $browser->script('document.querySelector(\'#tab-5d98bc46db9578b7426519bfadf8d1cd-1-label\').click()');

            $browser->type('password', 'new_admin');
            $browser->type('password_confirmation', 'new_admin');

            $browser->script('document.querySelector(\'[data-click="submit"]\').click()');

            $browser->waitFor('.swal2-success-ring')
                ->assertSee('Successfully created!')
                ->assertSee('new_admin@admin.com');
        });

        $this->browse(function (Browser $browser) {

            if($browser->element('[data-original-title="Logout"]')) {
                $browser->visitRoute('admin.profile.logout');
            }

            $browser->visitRoute('admin.login')
                ->type('login', 'new_admin')
                ->type('password', 'new_admin')
                ->press('Sign In')
                ->assertSee('new_admin@admin.com');
        });

        $this->browse(function (Browser $browser) {

            if($browser->element('[data-original-title="Logout"]')) {
                $browser->visitRoute('admin.profile.logout');
            }

            $browser->visitRoute('admin.login')
                ->type('login', 'admin')
                ->type('password', 'admin')
                ->press('Sign In')
                ->assertSee('admin@admin.com');
        });

        $this->browse(function (Browser $browser) {

            $browser->visitRoute('admin.administration.admin_user.index')
                ->assertSee('Administrators');

            $browser->script('document.querySelector(\'[data-original-title="Delete"]\').click()');

            $browser->waitFor('.swal2-question')
                ->assertSee('Delete ID:');

            $browser->script('document.querySelector(\'.swal2-confirm\').click()');

            $browser->pause(1000);

            $browser->waitFor('.swal2-success-ring')
                ->assertSee('Successfully deleted!');

            $browser->script('document.querySelector(\'.swal2-confirm\').click()');

            $browser->assertDontSee('new_admin@admin.com');

            $browser->script('document.querySelector(\'[data-original-title="Deleted"]\').click()');

            $browser->pause(1000);

            $browser->script('document.querySelector(\'[data-original-title="Restore"]\').click()');

            $browser->pause(500);

            $browser->script('document.querySelector(\'.swal2-confirm\').click()');

            $browser->waitFor('.swal2-success-ring')
                ->assertSee('Successfully restored!');

            $browser->visitRoute('admin.administration.admin_user.index')
                ->assertSee('Administrators');

            $browser->assertSee('new_admin@admin.com');

            $browser->script('document.querySelector(\'[data-original-title="Delete"]\').click()');

            $browser->waitFor('.swal2-question')
                ->assertSee('Delete ID:');

            $browser->script('document.querySelector(\'.swal2-confirm\').click()');

            $browser->pause(1000);

            $browser->waitFor('.swal2-success-ring')
                ->assertSee('Successfully deleted!');

            $browser->script('document.querySelector(\'.swal2-confirm\').click()');

            $browser->assertDontSee('new_admin@admin.com');

            $browser->script('document.querySelector(\'[data-original-title="Deleted"]\').click()');

            $browser->pause(1000);

            $browser->script('document.querySelector(\'[data-original-title="Delete forever"]\').click()');

            $browser->pause(500);

            $browser->script('document.querySelector(\'.swal2-confirm\').click()');

            $browser->visitRoute('admin.administration.admin_user.index')
                ->assertSee('Administrators');

            $browser->assertDontSee('new_admin@admin.com');
        });
    }

    public function test_sorting_administrator_list()
    {
        $this->adminAuth();

        $this->browse(function (Browser $browser) {

                $browser->visitRoute('admin.administration.admin_user.index')
                    ->assertSee('Administrators');

                $browser->script('document.querySelector(\'[data-sort="email"]\').click()');

                $browser->pause(1000);

                $browser->script('document.querySelector(\'[data-sort="login"]\').click()');

                $browser->pause(1000);

                $browser->script('document.querySelector(\'[data-sort="name"]\').click()');

                $browser->pause(1000);

                $browser->script('document.querySelector(\'[data-sort="updated_at"]\').click()');

                $browser->pause(1000);

                $browser->script('document.querySelector(\'[data-sort="created_at"]\').click()');

                $browser->pause(1000);

                $browser->script('document.querySelector(\'[data-sort="id"]\').click()');

                $browser->pause(1000);

                $browser->assertScript("Number(String($('table').find('tbody tr td').first().find('span')[0].innerHTML).trim()) === 1");

                $browser->script('document.querySelector(\'[data-sort="id"]\').click()');

                $browser->pause(1000);

                $browser->assertScript("Number(String($('table').find('tbody tr td').first().find('span')[0].innerHTML).trim()) !== 1");
            });
    }
}
