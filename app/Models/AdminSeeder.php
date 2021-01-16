<?php

namespace Admin\Models;

use Bfg\Dev\Commands\BfgDumpCommand;
use Illuminate\Database\Seeder;

/**
 * Class AdminSeeder
 * @package Admin\Models
 */
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (class_exists(BfgDumpCommand::$file_name)) {

            return ;
        }

        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        /** @var AdminUser $user_model */
        $user_model = config('admin.auth.providers.admin.model');

        // create a user.
        $user_model::truncate();

        $user_model::create([
            'login' => 'root',
            'password' => bcrypt('root'),
            'name'     => 'Root',
            'email'    => 'root@root.com'
        ]);

        $user_model::create([
            'login' => 'admin',
            'password' => bcrypt('admin'),
            'name'     => 'Admin',
            'email'    => 'admin@admin.com'
        ]);

        $user_model::create([
            'login' => 'moderator',
            'password' => bcrypt('moderator'),
            'name'     => 'Moderator',
            'email'    => 'moderator@moderator.com'
        ]);

        // create a role.
        AdminRole::truncate();

        AdminRole::create([
            'name' => 'Root',
            'slug' => 'root',
        ]);
        AdminRole::create([
            'name' => 'Administrator',
            'slug' => 'admin',
        ]);
        AdminRole::create([
            'name' => 'Moderator',
            'slug' => 'moderator',
        ]);

        $user_model::find(1)->roles()->save(AdminRole::find(1));
        $user_model::find(2)->roles()->save(AdminRole::find(2));
        $user_model::find(3)->roles()->save(AdminRole::find(3));

        AdminPermission::create(['path' => 'system*', 'method' => ['*'], 'state' => 'close', 'admin_role_id' => 3]);

        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
