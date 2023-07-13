<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAdminSettings extends Migration
{
    protected $connection = 'admin-sqlite';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (config('admin.functional.settings')) {
            if (!Schema::hasTable('admin_settings')) {
                Schema::create('admin_settings', static function (Blueprint $table) {
                    $table->bigIncrements('id');
                    $table->string('group')->default('admin.global_settings');
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (config('admin.functional.settings')) {
            Schema::dropIfExists('admin_settings');
        }
    }
}
