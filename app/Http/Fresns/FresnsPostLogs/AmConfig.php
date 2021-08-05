<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsPostLogs;

// 配置
use App\Base\Config\BaseConfig;

// use App\Http\Config\AssetFileConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Http\Fresns\FresnsFiles\FresnsFilesConfig;

class AmConfig extends BaseConfig
{
    // 主表
    const CFG_TABLE = 'post_logs';

    // 主表额外搜索字段
    const ADDED_SEARCHABLE_FIELDS = [
        'inStatus' => ['field' => 'status', 'op' => 'IN'],
        'logId' => ['field' => 'id', 'op' => '='],
        'ids' => ['field' => 'id', 'op' => 'IN'],
        'post_id' => ['field' => 'post_id', 'op' => '='],
        'member_id' => ['field' => 'member_id', 'op' => '='],
    ];

    // model 使用 - 表单映射
    const FORM_FIELDS_MAP = [
        'id' => 'id',
        'member_id' => 'member_id',
        'post_id' => 'post_id',
        'platform_id' => 'platform_id',
        'group_id' => 'group_id',
        'type' => 'type',
        'title' => 'title',
        'content' => 'content',
        'is_markdown' => 'is_markdown',
        'is_anonymous' => 'is_anonymous',
        'editor_json' => 'editor_json',
        'comment_set_json' => 'comment_set_json',
        'allow_json' => 'allow_json',
        'location_json' => 'location_json',
        'files_json' => 'files_json',
        'extends_json' => 'extends_json',
        'status' => 'status',
        'reason' => 'reason',
        'submit_at' => 'submit_at',
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