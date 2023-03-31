<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAdminRoleUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_role_user', static function (Blueprint $table) {

            $table->foreignId('admin_role_id')
                ->constrained('admin_roles')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('admin_user_id')
                ->constrained('admin_users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
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
