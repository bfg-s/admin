<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAdminLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_logs', static function (Blueprint $table) {
            $table->string('title');
            $table->text('detail')->nullable();
            $table->string('ip', 64);
            $table->string('url', 600)->nullable();
            $table->string('route')->nullable();
            $table->string('method', 16)->nullable();
            $table->string('user_agent', 500);
            $table->string('session_id', 250);
            $table->foreignId('admin_user_id')
                ->nullable()
                ->constrained('admin_users')
                ->nullOnDelete()->cascadeOnUpdate();
            $table->bigInteger('web_id')->nullable();
            $table->string('icon')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_logs');
    }
}
