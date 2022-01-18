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
        Schema::create('lte_role_user', function (Blueprint $table) {
            $table->unsignedBigInteger('lte_role_id');

            $table->unsignedBigInteger('lte_user_id');

            $table->timestamps();

            $table->foreign('lte_role_id')->references('id')->on('lte_roles')->onDelete('cascade');

            $table->foreign('lte_user_id')->references('id')->on('lte_users')->onDelete('cascade');
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
