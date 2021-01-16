<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAdminFileStorage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_file_storage', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->string("original_name");

            $table->string("file_name");

            $table->string("mime_type", 255);

            $table->string("size");

            $table->string("form")->nullable();

            $table->string("field")->nullable();

            $table->string("driver", 32)->default("admin");

            $table->string("driver_path")->default("/");

            $table->smallInteger("active")->default(1);

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
        Schema::dropIfExists('admin_file_storage');
    }
}
