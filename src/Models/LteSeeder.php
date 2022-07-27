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

        LteSetting::updateOrCreate([
            'group' => 'General',
            'title' => 'lte.settings_site_name',
            'type' => 'input',
            'name' => 'site_name',
            'value' => config('app.name'),
            'description' => 'lte.settings_site_name_description',
        ]);

        LteSetting::updateOrCreate([
            'group' => 'General',
            'title' => 'lte.settings_timezone',
            'type' => 'input',
            'name' => 'app.timezone',
            'value' => config('app.timezone'),
            'description' => 'lte.settings_timezone_description',
        ]);

        LteSetting::updateOrCreate([
            'group' => 'General',
            'title' => 'lte.settings_locale',
            'type' => 'input',
            'name' => 'app.locale',
            'value' => config('app.locale'),
            'description' => 'lte.settings_locale_description',
        ]);

        LteSetting::updateOrCreate([
            'group' => 'Admin',
            'title' => 'lte.settings_dark_mode',
            'type' => 'switcher',
            'name' => 'lte.dark_mode',
            'value' => config('lte.dark_mode'),
            'description' => 'lte.settings_dark_mode_description',
        ]);
    }
}
