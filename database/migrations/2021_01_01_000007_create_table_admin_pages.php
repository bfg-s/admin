<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAdminPages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_pages', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->unsignedBigInteger('parent_id')->nullable();

            $table->bigInteger('order');

            $table->string('icon')->nullable();

            $table->string('title');

            $table->text('description')->nullable();

            $table->string('action')->default('javascript:;');

            $table->string('type')->default('link'); // link, modal, call

            $table->string('target')->default('self'); // self, blank

            $table->string('position')->default('menu'); // menu, bottom, navbar

            $table->boolean('active')->default(true);

            $table->timestamps();
        });

        Schema::table('admin_pages', function (Blueprint $table) {

            $table->foreign('parent_id')->references('id')
                ->on('admin_pages')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_pages');
    }
}
