<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableLteFunctions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lte_functions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('slug')->unique();

            $table->text('description')->nullable();

            $table->boolean('active')->default(1);

            $table->timestamps();
        });

        Schema::create('lte_role_function', function (Blueprint $table) {
            $table->unsignedBigInteger('lte_role_id');

            $table->unsignedBigInteger('lte_function_id');

            $table->foreign('lte_role_id')->references('id')->on('lte_roles')->onDelete('cascade');

            $table->foreign('lte_function_id')->references('id')->on('lte_functions')->onDelete('cascade');
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
        Schema::dropIfExists('lte_role_function');
    }
}
