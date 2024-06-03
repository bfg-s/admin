<?php

declare(strict_types=1);

namespace Admin\Models;

use DB;
use Illuminate\Database\Seeder;

/**
 * Class for seeding all admin panel data.
 */
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        if (!app()->runningUnitTests()) {
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

        if (!app()->runningUnitTests()) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
