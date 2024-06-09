<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admin_dashboard_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_user_id')
                ->constrained('admin_users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('admin_dashboard_id')
                ->constrained('admin_dashboards')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->integer('order')->default(0);
            $table->json('widgets')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_dashboard_rows');
    }
};
