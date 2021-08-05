<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsHashtags;

// 配置
use App\Base\Config\BaseConfig;

// use App\Http\Config\AssetFileConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Http\Fresns\FresnsFiles\FresnsFilesConfig;

class AmConfig extends BaseConfig
{
    // 主表
    const CFG_TABLE = 'hashtags';

    // 主表额外搜索字段
    const ADDED_SEARCHABLE_FIELDS = [
        'huri' => ['field' => 'slug', 'op' => '='],
        'viewCountGt' => ['field' => 'view_count', 'op' => '>='],
        'viewCountLt' => ['field' => 'view_count', 'op' => '<='],
        'likeCountGt' => ['field' => 'like_count', 'op' => '>='],
        'likeCountLt' => ['field' => 'like_count', 'op' => '<='],
        'followCountGt' => ['field' => 'follow_count', 'op' => '>='],
        'followCountLt' => ['field' => 'follow_count', 'op' => '<='],
        'shieldCountGt' => ['field' => 'shield_count', 'op' => '>='],
        'shieldCountLt' => ['field' => 'shield_count', 'op' => '<='],
        'postCountGt' => ['field' => 'post_count', 'op' => '>='],
        'postCountLt' => ['field' => 'post_count', 'op' => '<='],
        'essenceCountGt' => ['field' => 'essence_count', 'op' => '>='],
        'essenceCountLt' => ['field' => 'essence_count', 'op' => '<='],
    ];

    // model 使用 - 表单映射
    const FORM_FIELDS_MAP = [
        'id' => 'id',
        'uuid' => 'uuid',
        'name' => 'name',
        'description' => 'description',
        'cover_file_id' => 'cover_file_id',
        'cover_file_url' => 'cover_file_url',
        'member_id' => 'member_id',
        'view_count' => 'view_count',
        'like_count' => 'like_count',
        'follow_count' => 'follow_count',
        'shield_count' => 'shield_count',
        'post_count' => 'post_count',
        'comment_count' => 'comment_count',
        'essence_count' => 'essence_count',
        'is_enable' => 'is_enable',
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