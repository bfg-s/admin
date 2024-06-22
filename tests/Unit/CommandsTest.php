<?php

namespace Admin\Tests;


use Admin\Tests\Unit\Models\User;

class CommandsTest extends TestCase
{
    public function test_lte_install_migrations_command()
    {
        $this->test_lte_install_command(['--migrate' => true]);
    }

    public function test_lte_install_command(array $options = [])
    {
        $this->artisan('admin:install', $options)
            ->assertExitCode(0);
    }

    public function test_lte_install_extension_command()
    {
        $this->test_lte_install_command(['--extension' => true]);
    }

    public function test_lte_make_user_admin_command()
    {
        $this->test_lte_make_user_root_command(2);
    }

    public function test_lte_make_user_root_command(int $role_id = 1)
    {
        $this->artisan('admin:user', [
            'email' => 'test@email.com',
            'name' => 'test',
            'password' => 'test',
            'role_id' => $role_id,
        ])->assertExitCode(0);
    }

    public function test_lte_make_user_moderator_command()
    {
        $this->test_lte_make_user_root_command(3);
    }

    public function test_lte_make_controller_without_model_command()
    {
        $this->artisan('admin:controller', [
            'name' => 'NoModel',
            '--force' => true,
        ])->assertExitCode(0);

        $file = admin_app_path('Controllers/NoModelController.php');

        $this->assertTrue(is_file($file));

        unlink($file);
    }

    public function test_lte_make_controller_with_model_command()
    {
        $this->artisan('admin:controller', [
            'name' => 'WithModel',
            '--model' => User::class,
            '--force' => true,
        ])->assertExitCode(0);

        $file = admin_app_path('Controllers/WithModelController.php');

        $this->assertTrue(is_file($file));

        unlink($file);
    }
}
