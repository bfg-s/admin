<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAdminBrowsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_browsers', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('ip', 64);
            $table->string('user_agent', 500);
            $table->string('session_id', 250);
            $table->json('notification_settings')->nullable();
            $table->boolean('active')->default(1);
            $table->foreignId('admin_user_id')
                ->nullable()
                ->constrained('admin_users')
                ->nullOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('admin_browsers');
    }
}
