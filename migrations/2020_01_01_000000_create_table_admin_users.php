<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAdminUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_users', static function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('login', 190)->unique();

            $table->string('email')->unique();

            $table->string('name')->nullable();

            $table->string('avatar')->nullable();

            $table->string('password', 60);

            $table->string('remember_token', 100)->nullable();

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_users');
    }
}
