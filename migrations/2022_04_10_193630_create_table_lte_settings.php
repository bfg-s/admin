<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableLteSettings extends Migration
{
    protected $connection = 'lte-sqlite';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('lte_settings')) {

            Schema::create('lte_settings', static function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('group')->default('lte.global_settings');
                $table->string('title');
                $table->string('type')->default('input');
                $table->string('name');
                $table->text('value')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lte_settings');
    }
}
