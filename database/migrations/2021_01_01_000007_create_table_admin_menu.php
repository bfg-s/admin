<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAdminMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_menu', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->unsignedBigInteger('parent_id')->nullable();

            $table->bigInteger('order');

            $table->string('icon')->nullable();

            $table->string('title');

            $table->string('action')->default('javascript:;');

            $table->string('type')->default('menu'); // menu, link, modal, call

            $table->string('target')->default('self'); // self, blank

            $table->boolean('active')->default(true);

            $table->timestamps();
        });

        Schema::table('admin_menu', function (Blueprint $table) {

            $table->foreign('parent_id')->references('id')
                ->on('admin_menu')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_menu');
    }
}
