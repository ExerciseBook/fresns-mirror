<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsEnableToUserFollowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('user_follows', 'is_enable')) {
            Schema::table('user_follows', function (Blueprint $table) {
                $table->unsignedTinyInteger('is_enable')->after('is_mutual')->default(1);
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
        if (Schema::hasColumn('user_follows', 'is_enable')) {
            Schema::table('user_follows', function (Blueprint $table) {
                $table->dropColumn('is_enable');
            });
        }
    }
}
