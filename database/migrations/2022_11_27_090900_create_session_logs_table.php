<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('session_logs', 'app_id')) {
            Schema::table('session_logs', function (Blueprint $table) {
                $table->char('app_id', 8)->after('version')->nullable();
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
        if (Schema::hasColumn('session_logs', 'app_id')) {
            Schema::table('session_logs', function (Blueprint $table) {
                $table->dropColumn('app_id');
            });
        }
    }
}
