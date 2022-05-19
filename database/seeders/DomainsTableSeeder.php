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
                'icon_file_id' => null,
                'icon_file_url' => null,
                'post_count' => 0,
                'comment_count' => 0,
                'is_enable' => 1,
                'created_at' => '2022-05-08 13:00:00',
                'updated_at' => '2022-05-08 13:00:00',
                'deleted_at' => null,
            ],
            1 => [
                'id' => 2,
                'domain' => 'fresns.org',
                'host' => 'fresns.org',
                'icon_file_id' => null,
                'icon_file_url' => null,
                'post_count' => 0,
                'comment_count' => 0,
                'is_enable' => 1,
                'created_at' => '2022-05-08 13:00:00',
                'updated_at' => '2022-05-08 13:00:00',
                'deleted_at' => null,
            ],
            2 => [
                'id' => 3,
                'domain' => 'fresns.org',
                'host' => 'docs.fresns.org',
                'icon_file_id' => null,
                'icon_file_url' => null,
                'post_count' => 0,
                'comment_count' => 0,
                'is_enable' => 1,
                'created_at' => '2022-05-08 13:00:00',
                'updated_at' => '2022-05-08 13:00:00',
                'deleted_at' => null,
            ],
            3 => [
                'id' => 4,
                'domain' => 'fresns.org',
                'host' => 'apps.fresns.org',
                'icon_file_id' => null,
                'icon_file_url' => null,
                'post_count' => 0,
                'comment_count' => 0,
                'is_enable' => 1,
                'created_at' => '2022-05-08 13:00:00',
                'updated_at' => '2022-05-08 13:00:00',
                'deleted_at' => null,
            ],
            4 => [
                'id' => 5,
                'domain' => 'fresns.org',
                'host' => 'discuss.fresns.org',
                'icon_file_id' => null,
                'icon_file_url' => null,
                'post_count' => 0,
                'comment_count' => 0,
                'is_enable' => 1,
                'created_at' => '2022-05-08 13:00:00',
                'updated_at' => '2022-05-08 13:00:00',
                'deleted_at' => null,
            ],
            5 => [
                'id' => 6,
                'domain' => 'fresns.cn',
                'host' => 'fresns.cn',
                'icon_file_id' => null,
                'icon_file_url' => null,
                'post_count' => 0,
                'comment_count' => 0,
                'is_enable' => 1,
                'created_at' => '2022-05-08 13:00:00',
                'updated_at' => '2022-05-08 13:00:00',
                'deleted_at' => null,
            ],
            6 => [
                'id' => 7,
                'domain' => 'fresns.cn',
                'host' => 'docs.fresns.cn',
                'icon_file_id' => null,
                'icon_file_url' => null,
                'post_count' => 0,
                'comment_count' => 0,
                'is_enable' => 1,
                'created_at' => '2022-05-08 13:00:00',
                'updated_at' => '2022-05-08 13:00:00',
                'deleted_at' => null,
            ],
            7 => [
                'id' => 8,
                'domain' => 'fresns.cn',
                'host' => 'apps.fresns.cn',
                'icon_file_id' => null,
                'icon_file_url' => null,
                'post_count' => 0,
                'comment_count' => 0,
                'is_enable' => 1,
                'created_at' => '2022-05-08 13:00:00',
                'updated_at' => '2022-05-08 13:00:00',
                'deleted_at' => null,
            ],
            8 => [
                'id' => 9,
                'domain' => 'fresns.cn',
                'host' => 'discuss.fresns.cn',
                'icon_file_id' => null,
                'icon_file_url' => null,
                'post_count' => 0,
                'comment_count' => 0,
                'is_enable' => 1,
                'created_at' => '2022-05-08 13:00:00',
                'updated_at' => '2022-05-08 13:00:00',
                'deleted_at' => null,
            ],
            9 => [
                'id' => 10,
                'domain' => 'tangjie.me',
                'host' => 'tangjie.me',
                'icon_file_id' => null,
                'icon_file_url' => null,
                'post_count' => 0,
                'comment_count' => 0,
                'is_enable' => 1,
                'created_at' => '2022-05-08 13:00:00',
                'updated_at' => '2022-05-08 13:00:00',
                'deleted_at' => null,
            ],
        ]);
    }
}
