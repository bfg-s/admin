<?php

namespace Lar\LteAdmin\Models;

use Illuminate\Database\Seeder;

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
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // create a user.
        LteUser::truncate();

        LteUser::create([
            'login' => 'root',
            'password' => bcrypt('root'),
            'name'     => 'Root',
            'email'    => 'root@root.com'
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

        LteUser::first()->roles()->save(LteRole::first());

        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
