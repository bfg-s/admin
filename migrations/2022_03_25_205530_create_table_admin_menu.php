<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAdminMenu extends Migration
{
    protected $connection = 'admin-sqlite';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (config('admin.functional.menu')) {
            if (!Schema::hasTable('admin_menu')) {
                Schema::create('admin_menu', static function (Blueprint $table) {
                    $table->bigIncrements('id');
                    $table->string('name');
                    $table->string('icon');
                    $table->string('route');
                    $table->string('action')->nullable();
                    $table->enum('type', ['item', 'resource', 'group'])->default('item');
                    $table->text('except')->nullable();
                    $table->integer('order')->default(0);
                    $table->boolean('active')->default(1);
                    $table->foreignId('parent_id')
                        ->nullable()
                        ->constrained('admin_menu')
                        ->nullOnDelete()->cascadeOnUpdate();
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
        if (config('admin.functional.menu')) {
            Schema::dropIfExists('admin_menu');
        }
    }
}
