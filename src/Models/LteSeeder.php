<?php

namespace Lar\LteAdmin\Models;

use Illuminate\Database\Seeder;
use Lar\LteAdmin\Commands\LteDbDumpCommand;

/**
 * Class LteSeeder
 *
 * @package Lar\LteAdmin\Models
 */
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

            return ;
        }

        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        /** @var LteUser $user_model */
        $user_model = config('lte.auth.providers.lte.model');

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
        LteRole::truncate();

        LteRole::create([
            'name' => 'Root',
            'slug' => 'root',
        ]);
        LteRole::create([
            'name' => 'Administrator',
            'slug' => 'admin',
        ]);
        LteRole::create([
            'name' => 'Moderator',
            'slug' => 'moderator',
        ]);

        $user_model::find(1)->roles()->save(LteRole::find(1));
        $user_model::find(2)->roles()->save(LteRole::find(2));
        $user_model::find(3)->roles()->save(LteRole::find(3));

        LtePermission::create(['path' => 'admin*', 'method' => ['*'], 'state' => 'close', 'lte_role_id' => 3]);

        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
