<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('type')->default(1);
            $table->string('code', 32);
            $table->string('style', 64);
            $table->string('name', 128)->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('image_file_id')->nullable();
            $table->string('image_file_url')->nullable();
            $table->unsignedBigInteger('image_active_file_id')->nullable();
            $table->string('image_active_file_url')->nullable();
            $table->unsignedTinyInteger('display_type')->default(1);
            $table->string('plugin_unikey', 64);
            $table->unsignedTinyInteger('is_enable')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operations');
    }
}
