<?php

namespace Lar\LteAdmin\Commands;

use Illuminate\Console\Command;
use Lar\LteAdmin\Models\LteUser;

/**
 * Class MakeUser
 *
 * @package Lar\Admin\Commands
 */
class MakeUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lte:make-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make admin user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $email = null;

        $password = null;

        $password2 = null;

        if (!$email) {

            $email = $this->ask("Enter a admin E-Mail");
        }

        if (!$password) {

            $password = $this->ask("Enter a password");
        }

        if (!$password2) {

            $password2 = $this->ask("Enter a confirmation password");
        }

        if ($password !== $password2) {

            $this->error("Admin passwords not match!");

            return ;
        }

        $name = explode("@", $email)[0];

        /** @var LteUser $user_model */
        $user_model = config('lte.auth.providers.lte.model');

        if ($user_model::create([
            "username" => $name,
            "password" => bcrypt($password),
            "email" => $email,
            "name" => $name
        ])) {

            $this->info("User success created.");
        }

        else {

            $this->error("Error on user create!");
        }
    }
}
