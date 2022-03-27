<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableLteRoleUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lte_role_user', static function (Blueprint $table) {

            $table->foreignId('lte_role_id')
                ->constrained('lte_roles')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('lte_user_id')
                ->constrained('lte_users')
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
        Schema::dropIfExists('lte_role_user');
    }
}
