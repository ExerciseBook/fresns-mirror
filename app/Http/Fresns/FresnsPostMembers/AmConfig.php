<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsPostMembers;

// 配置
use App\Base\Config\BaseConfig;

// use App\Http\Config\AssetFileConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Http\Fresns\FresnsFiles\FresnsFilesConfig;

class AmConfig extends BaseConfig
{
    // 主表
    const CFG_TABLE = 'post_members';

    // 主表额外搜索字段
    const ADDED_SEARCHABLE_FIELDS = [
        'post_id' => ['field' => 'post_id', 'op' => '=']
    ];

    // model 使用 - 表单映射
    const FORM_FIELDS_MAP = [
        'id' => 'id',
        'post_id' => 'post_id',
        'member_id' => 'member_id',
        'plugin_unikey' => 'plugin_unikey',
        'object_id' => 'object_id',
        'more_json' => 'more_json',
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
            'rank_num' => 'numeric',
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