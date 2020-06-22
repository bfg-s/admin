<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableLteFunctionsClass extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lte_functions', function (Blueprint $table) {
            $table->string('class')->nullable()->after('slug');
            $table->dropUnique(['slug']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lte_functions', function (Blueprint $table) {
            $table->dropColumn('class');
            $table->unique('slug');
        });
    }
}
