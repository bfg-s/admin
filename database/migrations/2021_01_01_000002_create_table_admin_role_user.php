<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAdminRoleUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_role_user', function (Blueprint $table) {

            $table->unsignedBigInteger('admin_role_id');

            $table->unsignedBigInteger('admin_user_id');

            $table->timestamps();

            $table->foreign('admin_role_id')->references('id')->on('admin_roles')->onDelete('cascade');

            $table->foreign('admin_user_id')->references('id')->on('admin_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_role_user');
    }
}
