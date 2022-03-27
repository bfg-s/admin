<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableLteMenu extends Migration
{
    protected $connection = 'lte-sqlite';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('lte_menu')) {

            Schema::create('lte_menu', static function (Blueprint $table) {
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
                    ->constrained('lte_menu')
                    ->nullOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('lte_menu');
    }
}
