<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSessionTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('session_tokens', 'user_token')) {
            Schema::table('session_tokens', function (Blueprint $table) {
                $table->dropUnique('account_token');

                $table->string('version', 16)->nullable()->after('platform_id');
                $table->char('app_id', 8)->nullable()->after('platform_id');
                $table->char('user_token', 32)->nullable()->after('user_id');

                $table->renameColumn('token', 'account_token');

                $table->unique(['account_id', 'account_token'], 'account_token');
                $table->unique(['user_id', 'user_token'], 'user_token');
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
        if (Schema::hasColumn('session_tokens', 'user_token')) {
            Schema::table('session_tokens', function (Blueprint $table) {
                $table->dropColumn('version');
                $table->dropColumn('app_id');
                $table->dropColumn('account_token');

                $table->renameColumn('account_token', 'token');

                $table->dropUnique('account_token');
                $table->dropUnique('user_token');
            });
        }
    }
}
