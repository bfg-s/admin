<?php

namespace Admin\Models;

use DB;
use Illuminate\Database\Seeder;
use Admin\Commands\AdminDbDumpCommand;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (class_exists(AdminDbDumpCommand::$file_name)) {
            return;
        }

        if (! app()->runningUnitTests()) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        /** @var AdminUser $user_model */
        $user_model = config('admin.auth.providers.admin.model', AdminUser::class);

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
            'path' => 'admin*', 'method' => ['*'], 'state' => 'close', 'admin_role_id' => $moderatorRole->id
        ]);

        if (! app()->runningUnitTests()) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        AdminSetting::updateOrCreate([
            'group' => 'General',
            'title' => 'Site name',
            'type' => 'input',
            'name' => 'site_name',
            'value' => config('app.name'),
            'description' => 'Application name',
        ]);

        AdminSetting::updateOrCreate([
            'group' => 'General',
            'title' => 'Timezone',
            'type' => 'input',
            'name' => 'app.timezone',
            'value' => config('app.timezone'),
            'description' => 'Default application timezone',
        ]);

        AdminSetting::updateOrCreate([
            'group' => 'General',
            'title' => 'Locale',
            'type' => 'input',
            'name' => 'app.locale',
            'value' => config('app.locale'),
            'description' => 'Application default locale',
        ]);

        AdminSetting::updateOrCreate([
            'group' => 'Admin',
            'title' => 'Dark mode',
            'type' => 'switcher',
            'name' => 'admin.dark_mode',
            'value' => config('admin.dark_mode'),
            'description' => 'Admin dark mode by default',
        ]);
    }
}
