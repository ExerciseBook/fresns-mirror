<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsPluginUsages;

use App\Base\Config\BaseConfig;
use App\Http\Config\AssetFileConfig;
use App\Http\FresnsDb\FresnsFiles\FresnsFilesConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FsConfig extends BaseConfig
{
    // Main Table
    const CFG_TABLE = 'plugin_usages';

    // Additional search columns in the main table
    const ADDED_SEARCHABLE_FIELDS = [
        'type' => ['field' => 'type', 'op' => '='],
        'ids' => ['field' => 'id', 'op' => 'IN'],
        'group_id' => ['field' => 'group_id', 'op' => '='],
        'scene' => ['field' => 'scene', 'op' => 'LIKE'],
        'is_enable' => ['field' => 'is_enable', 'op' => '='],
    ];

    const IS_GROUP_ADMIN_OPTION = [
        ['key' => 0, 'text' => 'Disable'],
        ['key' => 1, 'text' => 'Enable'],
    ];

    const LANGUAGE_CODES = 'language_codes';
    const LANG_SETTINGS = 'language_menus';
    const DEFAULT_LANGUAGE = 'default_language';

    // Application Scenarios
    const SCONE_OPTION = [
        // ['key' => 0,'value' =>0,'name'=>'All', 'title' => 'All'],
        ['key' => '1', 'value' => '1', 'name' => 'Posts', 'title' => 'Posts'],
        ['key' => '2', 'value' => '2', 'name' => 'Comments ', 'title' => 'Comments '],
        ['key' => '3', 'value' => '3', 'name' => 'Members', 'title' => 'Members'],

    ];

    // Data source
    const SOURCE_PARAMETER = [
        ['apiName' => 'Get the list of posts', 'apiAddress' => '/api/fresns/post/lists', 'nickname' => 'postLists'],
        ['apiName' => 'Get posts from following', 'apiAddress' => '/api/fresns/post/follows', 'nickname' => 'postFollows'],
        ['apiName' => 'Get posts from nearby', 'apiAddress' => '/api/fresns/post/nearbys', 'nickname' => 'postNearbys'],
    ];
    // Member role tips
    const ROLE_MEMBERS_TIPS = 'Leave blank means all members have access';

    // Number of applications tips
    const EDITER_NUMBER_TIPS = "To 'poll' plugin, for example, the number of 2 means that a single post can be accompanied by 2 polls";

    // Group administrator tips
    const IS_ADMIN_TIPS = 'When enabled, only group administrators will show the plugin';

    // Extensions Type
    const TYPE_OPTION = [
        ['key' => 1, 'text' => 'Wallet Income'],
        ['key' => 2, 'text' => 'Wallet Expenses'],
        ['key' => 3, 'text' => 'Editor Extensions'],
        ['key' => 4, 'text' => 'Search Type Extensions'],
        ['key' => 5, 'text' => 'Management Extensions'],
        ['key' => 6, 'text' => 'Group Extensions'],
        ['key' => 7, 'text' => 'Member Feature Extensions'],
        ['key' => 8, 'text' => 'Member Profile Extensions'],
        ['key' => 9, 'text' => 'Map Extensions'],
    ];

    // Model Usage - Form Mapping
    const FORM_FIELDS_MAP = [
        'id' => 'id',
        'name' => 'name',
        'rank_num' => 'rank_num',
        'is_enable' => 'is_enable',
        'remark' => 'remark',
        'type' => 'type',
        'icon_file_id' => 'icon_file_id',
        'icon_file_url' => 'icon_file_url',
        'more_json' => 'more_json',
        'type' => 'type',
        'group_id' => 'group_id',
        'editor_number' => 'editor_number',
        'plugin_unikey' => 'plugin_unikey',
        'scene' => 'scene',
        'parameter' => 'parameter',
        'member_roles' => 'member_roles',
        'can_delete' => 'can_delete',
        'is_group_admin' => 'is_group_admin',
        'data_sources' => 'data_sources',
    ];
}
