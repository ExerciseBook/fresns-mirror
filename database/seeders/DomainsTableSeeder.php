<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DomainsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('domains')->delete();

        \DB::table('domains')->insert([
            0 => [
                'id' => 1,
                'domain' => 'fresns.com',
                'host' => 'fresns.com',
                'post_count' => 0,
                'comment_count' => 0,
                'is_enable' => 1,
                'created_at' => '2022-10-18 17:00:00',
                'updated_at' => null,
                'deleted_at' => null,
            ],
            1 => [
                'id' => 2,
                'domain' => 'fresns.org',
                'host' => 'fresns.org',
                'post_count' => 0,
                'comment_count' => 0,
                'is_enable' => 1,
                'created_at' => '2022-10-18 17:00:00',
                'updated_at' => null,
                'deleted_at' => null,
            ],
            2 => [
                'id' => 3,
                'domain' => 'fresns.org',
                'host' => 'discuss.fresns.org',
                'post_count' => 0,
                'comment_count' => 0,
                'is_enable' => 1,
                'created_at' => '2022-10-18 17:00:00',
                'updated_at' => null,
                'deleted_at' => null,
            ],
            3 => [
                'id' => 4,
                'domain' => 'fresns.com',
                'host' => 'marketplace.fresns.com',
                'post_count' => 0,
                'comment_count' => 0,
                'is_enable' => 1,
                'created_at' => '2022-10-18 17:00:00',
                'updated_at' => null,
                'deleted_at' => null,
            ],
            4 => [
                'id' => 5,
                'domain' => 'fresns.com',
                'host' => 'developer.fresns.com',
                'post_count' => 0,
                'comment_count' => 0,
                'is_enable' => 1,
                'created_at' => '2022-10-18 17:00:00',
                'updated_at' => null,
                'deleted_at' => null,
            ],
            5 => [
                'id' => 6,
                'domain' => 'tangjie.me',
                'host' => 'tangjie.me',
                'post_count' => 0,
                'comment_count' => 0,
                'is_enable' => 1,
                'created_at' => '2022-10-18 17:00:00',
                'updated_at' => null,
                'deleted_at' => null,
            ],
        ]);
    }
}
