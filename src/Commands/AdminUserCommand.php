<?php

declare(strict_types=1);

namespace Admin\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Admin\Models\AdminRole;
use Admin\Models\AdminUser as User;

class AdminUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:user {email?} {name?} {password?} {role_id?}';

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
        /** @var User $user_model */
        $user_model = config('admin.auth.providers.admin.model');

        $email = $this->argument('email');

        $name = $this->argument('name');

        $password = $this->argument('password');

        $password2 = $this->argument('password');

        if (!$email) {
            $email = $this->ask('Enter a admin E-Mail');

            if ($user_model::where('email', $email)->first()) {
                $this->error("Admin with email [$email] is isset!");
                return 11;
            }
        }

        if (!$name) {
            $name = $this->ask('Enter a admin Login', explode('@', $email)[0]);

            if ($user_model::where('username', $name)->first()) {
                $this->error("Admin with name [$name] is isset!");
                return 10;
            }
        }

        if (!$password) {
            $password = $this->ask('Enter a password');
        }

        if (!$password2) {
            $password2 = $this->ask('Enter a confirmation password');
        }

        if ($password !== $password2) {
            $this->error('Admin passwords not match!');
            return 9;
        }

        if ($user_model::where([
            'email' => $email
        ])->exists()) {
            $this->error("User with email [{$email}] is exists!");
            return 8;
        }

        if ($user = $user_model::create([
            'username' => $name,
            'password' => bcrypt($password),
            'email' => $email,
            'name' => $name,
            'login' => Str::slug($name, '_'),
        ])) {
            $roles = AdminRole::all();
            $role = $this->argument('role_id');
            if (!$role) {
                $role = $this->choice(
                    'Select role for new user',
                    $roles->pluck('name', 'id')->toArray(),
                    'Root'
                );
                $role = $roles->where('name', $role)->first()->id;
            }
            $user->roles()->sync([$role]);

            $this->info('User success created.');
        } else {
            $this->error('Error on user create!');
            return 7;
        }
        return 0;
    }
}
