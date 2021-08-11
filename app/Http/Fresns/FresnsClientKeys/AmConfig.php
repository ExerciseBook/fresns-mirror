<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsClientKeys;

// 配置
use App\Base\Config\BaseConfig;
use App\Http\Config\AssetFileConfig;
use Illuminate\Validation\Rule;
use App\Http\Fresns\FresnsFiles\FresnsFilesConfig;

class AmConfig extends BaseConfig
{
    // 主表
    const CFG_TABLE = 'session_keys';

    // 主表额外搜索字段
    const ADDED_SEARCHABLE_FIELDS = [
        'platform_id' => ['field' => 'platform_id', 'op' => '=']
    ];
    const PLATFORMS = 'platforms';
    const IS_ENABLE_OPTION = [
        ['key' => 0, 'text' => '禁用'],
        ['key' => 1, 'text' => '启用']
    ];
    const TYPE_OPTION = [
        ['key' => 1, 'text' => '主程API'],
        ['key' => 2, 'text' => '插件API'],
    ];
    const PLATFORM_OPTION = [
        ['key' => 1, 'text' => 'other'],
        ['key' => 2, 'text' => 'PC Web'],
        ['key' => 3, 'text' => 'Mobile Web'],
        ['key' => 4, 'text' => 'iOS App'],
        ['key' => 5, 'text' => 'Android App'],
        ['key' => 6, 'text' => 'WeChat Web'],
        ['key' => 7, 'text' => 'WeChat MiniProgram'],
        ['key' => 8, 'text' => 'QQ MiniProgram'],
        ['key' => 9, 'text' => 'Alipay MiniApp'],
        ['key' => 10, 'text' => 'ByteDance MicroApp'],
        ['key' => 11, 'text' => 'Quick App'],
        ['key' => 12, 'text' => 'Baidu SmartProgram'],
        ['key' => 13, 'text' => '360 MiniApp'],
    ];
    // model 使用 - 表单映射
    const FORM_FIELDS_MAP = [
        'id' => 'id',
        'name' => 'name',
        // 'nickname'  => 'nickname',
        // 'rank_num'  => 'rank_num',
        'is_enable' => 'is_enable',
        'remark' => 'remark',
        'type' => 'type',
        'plugin_unikey' => 'plugin_unikey',
        // 'file_id'   => 'file_id',
        // 'file_url'  => 'file_url',
        // 'more_json'  => 'more_json',
        'alias_key' => 'alias_key',
        'platform_id' => 'platform_id',
        'app_id' => 'app_id',
        'app_secret' => 'app_secret',
    ];

    // 新增规则
    public function storeRule()
    {
        $table = self::CFG_TABLE;
        $assetFileTable = FresnsFilesConfig::CFG_TABLE;

        $rule = [
            'name' => [
                'required',
                Rule::unique($table)->where(function ($query) {
                    $query->where('deleted_at', null);
                })
            ],
            'nickname' => [
                'filled',
                Rule::unique($table)->where(function ($query) {
                    $query->where('deleted_at', null);
                })
            ],
            'is_enable' => Rule::in(BaseConfig::ENABLE_VALUE_ARR),
            'file_id' => Rule::exists($assetFileTable, 'id')->where(function ($query) {
                $query->where('deleted_at', null);
            }),
            'file_url' => "url",
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
                Rule::unique($table)->ignore($id)->where(function ($query) {
                    $query->where('deleted_at', null);
                })
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
            'more_json' => "json",
        ];

        return $rule;
    }

}