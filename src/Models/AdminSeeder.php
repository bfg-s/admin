<?php

namespace Admin\Models;

use DB;
use Illuminate\Database\Seeder;
use Admin\Commands\LteDbDumpCommand;

class AdminSeeder extends Seeder
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

        /** @var AdminUser $user_model */
        $user_model = config('admin.auth.providers.admin.model');

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
        AdminRole::truncate();

        $rootRole = AdminRole::create([
            'name' => 'Root',
            'slug' => 'root',
        ]);
        $adminRole = AdminRole::create([
            'name' => 'Administrator',
            'slug' => 'admin',
        ]);
        $moderatorRole = AdminRole::create([
            'name' => 'Moderator',
            'slug' => 'moderator',
        ]);

        $rootUser->roles()->save($rootRole);
        $adminUser->roles()->save($adminRole);
        $moderatorUser->roles()->save($moderatorRole);

        AdminPermission::create([
            'path' => 'admin*', 'method' => ['*'], 'state' => 'close', 'lte_role_id' => $moderatorRole->id
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        AdminSetting::updateOrCreate([
            'group' => 'General',
            'title' => 'admin.settings_site_name',
            'type' => 'input',
            'name' => 'site_name',
            'value' => config('app.name'),
            'description' => 'admin.settings_site_name_description',
        ]);

        AdminSetting::updateOrCreate([
            'group' => 'General',
            'title' => 'admin.settings_timezone',
            'type' => 'input',
            'name' => 'app.timezone',
            'value' => config('app.timezone'),
            'description' => 'admin.settings_timezone_description',
        ]);

        AdminSetting::updateOrCreate([
            'group' => 'General',
            'title' => 'admin.settings_locale',
            'type' => 'input',
            'name' => 'app.locale',
            'value' => config('app.locale'),
            'description' => 'admin.settings_locale_description',
        ]);

        AdminSetting::updateOrCreate([
            'group' => 'Admin',
            'title' => 'admin.settings_dark_mode',
            'type' => 'switcher',
            'name' => 'admin.dark_mode',
            'value' => config('admin.dark_mode'),
            'description' => 'admin.settings_dark_mode_description',
        ]);
    }
}
