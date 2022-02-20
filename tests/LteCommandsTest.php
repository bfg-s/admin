<?php

namespace LteAdmin\Tests;

class LteCommandsTest extends LteTest
{
    public function test_lte_install_command(array $options = [])
    {
        $this->artisan('lte:install', $options)
            ->assertExitCode(0);
    }

    public function test_lte_install_force_command()
    {
        $this->test_lte_install_command(['--force' => true]);
    }

    public function test_lte_install_migrations_command()
    {
        $this->test_lte_install_command(['--migrate' => true]);
    }

    public function test_lte_install_extension_command()
    {
        $this->test_lte_install_command(['--extension' => true]);
    }

    public function test_lte_make_user_command()
    {
        $this->artisan('lte:user', [
            'email' => 'test@email.com',
            'name' => 'test',
            'password' => 'test',
        ]);
    }
}
