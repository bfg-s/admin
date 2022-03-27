<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableLtePermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lte_permission', static function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('path')->default('*');

            $table->string('method', 1000)->default('["*"]');

            $table->enum('state', ['close', 'open'])->default('open');

            $table->string('description')->nullable();

            $table->foreignId('lte_role_id')
                ->constrained('lte_roles')
                ->cascadeOnDelete();

            $table->boolean('active')->default(1);

            $table->timestamps();
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
