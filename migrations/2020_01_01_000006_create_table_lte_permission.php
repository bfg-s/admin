<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLtePermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lte_permission', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->string('path')->default('*');

            $table->string('method')->default('["*"]');

            $table->enum('state', ['close', 'open'])->default('open');

            $table->unsignedBigInteger('lte_role_id');

            $table->boolean("active")->default(1);

            $table->timestamps();

            $table->foreign('lte_role_id')->references('id')->on('lte_roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lte_permission');
    }
}
