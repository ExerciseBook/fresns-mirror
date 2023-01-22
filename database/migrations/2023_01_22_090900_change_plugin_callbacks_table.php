<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePluginCallbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('plugin_callbacks', 'uuid')) {
            Schema::table('plugin_callbacks', function (Blueprint $table) {
                $table->renameColumn('uuid', 'ulid');
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
        if (Schema::hasColumn('plugin_callbacks', 'ulid')) {
            Schema::table('plugin_callbacks', function (Blueprint $table) {
                $table->renameColumn('ulid', 'uuid');
            });
        }
    }
}
