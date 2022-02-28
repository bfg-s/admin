<?php

namespace LteAdmin\Models;

use DB;
use Illuminate\Database\Seeder;
use LteAdmin\Commands\LteDbDumpCommand;

class LteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (class_exists(LteDbDumpCommand::$file_name)) {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        /** @var LteUser $user_model */
        $user_model = config('lte.auth.providers.lte.model');

        // create a user.
        $user_model::truncate();

        $rootUser = $user_model::create([
            'login' => 'root',
            'password' => bcrypt('root'),
            'name' => 'Root',
            'email' => 'root@root.com',
        ]);

        $adminUser = $user_model::create([
            'login' => 'admin',
            'password' => bcrypt('admin'),
            'name' => 'Admin',
            'email' => 'admin@admin.com',
        ]);

        $moderatorUser = $user_model::create([
            'login' => 'moderator',
            'password' => bcrypt('moderator'),
            'name' => 'Moderator',
            'email' => 'moderator@moderator.com',
        ]);

        // create a role.
        LteRole::truncate();

        $rootRole = LteRole::create([
            'name' => 'Root',
            'slug' => 'root',
        ]);
        $adminRole = LteRole::create([
            'name' => 'Administrator',
            'slug' => 'admin',
        ]);
        $moderatorRole = LteRole::create([
            'name' => 'Moderator',
            'slug' => 'moderator',
        ]);

        $rootUser->roles()->save($rootRole);
        $adminUser->roles()->save($adminRole);
        $moderatorUser->roles()->save($moderatorRole);

        LtePermission::create([
            'path' => 'admin*', 'method' => ['*'], 'state' => 'close', 'lte_role_id' => $moderatorRole->id
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
