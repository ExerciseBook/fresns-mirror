<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePostAppendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('post_appends', 'is_allow')) {
            Schema::table('post_appends', function (Blueprint $table) {
                $table->unsignedSmallInteger('is_allow')->default(1)->change();
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
        if (Schema::hasColumn('post_appends', 'is_allow')) {
            Schema::table('post_appends', function (Blueprint $table) {
                $table->unsignedSmallInteger('is_allow')->default(0)->change();
            });
        }
    }
}
