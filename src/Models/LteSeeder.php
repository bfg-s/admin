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

        $user_model::first()->roles()->save(LteRole::first());

        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
