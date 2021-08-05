<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsPluginUsages;

// 配置
use App\Base\Config\BaseConfig;
use App\Http\Config\AssetFileConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Http\Fresns\FresnsFiles\FresnsFilesConfig;

class AmConfig extends BaseConfig
{
    // 主表
    const CFG_TABLE = 'plugin_usages';

    // 主表额外搜索字段
    const ADDED_SEARCHABLE_FIELDS = [
        'type' => ['field' => 'type', 'op' => '='],
        'ids' => ['field' => 'id', 'op' => 'IN'],
        'group_id' => ['field' => 'group_id', 'op' => '='],
        'scene' => ['field' => 'scene', 'op' => 'LIKE'],
        'is_enable' => ['field' => 'is_enable', 'op' => '='],
    ];
    const TEST_SELECTED_OPTION = [
        ['key' => 0, 'text' => '禁用'],
        ['key' => 1, 'text' => '启用']
    ];
    const IS_GROUP_ADMIN_OPTION = [
        ['key' => 0, 'text' => '禁用'],
        ['key' => 1, 'text' => '启用']
    ];
    const GROUP_SELECTED_OPTION = [
        ['key' => 1, 'text' => '小组1'],
        ['key' => 2, 'text' => '小组2']
    ];
    const MULTILINGUAL_OPTION = [
        ['key' => 'zh-Hans', 'text' => '简体中文', 'nickname' => 'zh-CN'],
        ['key' => 'zh-Hant', 'text' => '繁体中文', 'nickname' => 'zh-TW'],
        ['key' => 'en', 'text' => '英文', 'nickname' => 'en-US'],
    ];
    const LANGUAGE_CODES = 'language_codes';
    const LANG_SETTINGS = 'languages';
    const DEFAULT_LANGUAGE = 'default_language';
    // scene(应用场景)
    const SCONE_OPTION = [
        // ['key' => 0,'value' =>0,'name'=>'全部', 'title' => '全部'],
        ['key' => '1', 'value' => '1', 'name' => '帖子', 'title' => '帖子'],
        ['key' => '2', 'value' => '2', 'name' => '评论 ', 'title' => '评论 '],
        ['key' => '3', 'value' => '3', 'name' => '用户', 'title' => '用户'],

    ];
    // 数据来源
    const SOURCE_PARAMETER = [
        ['apiName' => '获取帖子[列表]', 'apiAddress' => '/api/fresns/post/lists', 'nickname' => 'postLists'],
        ['apiName' => '获取帖子关注的[列表]', 'apiAddress' => '/api/fresns/post/follows', 'nickname' => 'postFollows'],
        ['apiName' => '获取帖子附近的[列表]', 'apiAddress' => '/api/fresns/post/nearbys', 'nickname' => 'postNearbys'],
    ];
    // 用户角色tips
    const ROLE_USERS_TIPS = '留空代表所有用户都有使用权';
    // 应用数量tips
    const EDITER_NUMBER_TIPS = "以'投票'插件为例,数量为2则代表单个帖子可以附带2个投票";
    // 小组管理员专用tips
    const IS_ADMIN_TIPS = "启用后,仅小组管理员会展示该插件";

    // 扩展类型
    const TYPE_OPTION = [
        ['key' => 1, 'text' => '支付服务商'],
        ['key' => 2, 'text' => '体现支持渠道'],
        ['key' => 3, 'text' => '编辑器扩展'],
        ['key' => 4, 'text' => '搜索类型扩展'],
        ['key' => 5, 'text' => '管理扩展'],
        ['key' => 6, 'text' => '小组扩展'],
        ['key' => 7, 'text' => '用户功能扩展'],
        ['key' => 8, 'text' => '用户资料扩展'],
    ];
    // model 使用 - 表单映射
    const FORM_FIELDS_MAP = [
        'id' => 'id',
        'name' => 'name',
        // 'nickname'  => 'nickname',
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
        // 'icon'  => 'icon',
        'scene' => 'scene',
        'parameter' => 'parameter',
        'member_roles' => 'member_roles',
        'can_delete' => 'can_delete',
        'is_group_admin' => 'is_group_admin',
        'data_sources' => 'data_sources',
    ];

    // 新增规则
    public function storeRule()
    {
        $table = self::CFG_TABLE;
        $assetFileTable = FresnsFilesConfig::CFG_TABLE;


        $rule = [
            'name' => [
                'required',
                // Rule::unique($table)->where(function ($query) {
                //     $query->where('deleted_at', null);
                // })
            ],
            'nickname' => [
                'filled',
                Rule::unique($table)->where(function ($query) {
                    $query->where('deleted_at', null);
                })
            ],
            'rank_num' => 'numeric',
            'is_enable' => Rule::in(BaseConfig::ENABLE_VALUE_ARR),
            'file_id' => Rule::exists($assetFileTable, 'id')->where(function ($query) {
                $query->where('deleted_at', null);
            }),
            'file_url' => "url",
            'editor_number' => "numeric|max:10",
            'more_json' => "json",
        ];

        return $rule;
    }

    // 更新规则
    public function updateRule()
    {
        $id = request()->input('id');
        $table = self::CFG_TABLE;
        $assetFileTable = FresnsFilesConfig::CFG_TABLE;


        $rule = [
            'id' => [
                'required',
                Rule::exists($table)->where(function ($query) {
                    $query->where('deleted_at', null);
                })
            ],
            'name' => [
                'filled',
                // Rule::unique($table)->ignore($id)->where(function ($query) {
                //     $query->where('deleted_at', null);
                // })
            ],
            'nickname' => [
                'filled',
                Rule::unique($table)->ignore($id)->where(function ($query) {
                    $query->where('deleted_at', null);
                })
            ],
            'rank_num' => 'numeric',
            'file_id' => Rule::exists($assetFileTable, 'id')->where(function ($query) {
                $query->where('deleted_at', null);
            }),
            'file_url' => "url",
            'editor_number' => "numeric|max:10",
            'more_json' => "json",
        ];

        return $rule;
    }

}