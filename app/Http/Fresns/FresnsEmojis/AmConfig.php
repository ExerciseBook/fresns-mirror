<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsEmojis;

// 配置
use App\Base\Config\BaseConfig;
use App\Http\Config\AssetFileConfig;

// use App\Plugins\Tweet\TweetFiles\TweetFilesConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Http\Fresns\FresnsFiles\FresnsFilesConfig;

class AmConfig extends BaseConfig
{
    // 主表
    const CFG_TABLE = 'emojis';

    // 主表额外搜索字段
    const ADDED_SEARCHABLE_FIELDS = [
        'parent_id' => ['field' => 'parent_id', 'op' => '=']
    ];

    //表情小组
    const TYPE_GROUP = 2;

    // model 使用 - 表单映射
    const FORM_FIELDS_MAP = [
        'id' => 'id',
        'name' => 'name',
        'nickname' => 'nickname',
        'rank_num' => 'rank_num',
        'is_enable' => 'is_enable',
        'remark' => 'remark',
        'type' => 'type',
        'image_file_id' => 'image_file_id',
        'image_file_url' => 'image_file_url',
        'more_json' => 'more_json',
        'code' => 'code',
        'type' => 'type',
        'parent_id' => 'parent_id',
    ];

    // 新增规则
    public function storeRule()
    {
        $table = self::CFG_TABLE;
        $assetFileTable = FresnsFilesConfig::CFG_TABLE;


        $rule = [
            // 'name'          => [
            //     'required',
            //     Rule::unique($table)->where(function ($query) {
            //         $query->where('deleted_at', null);
            //     })
            // ],
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
            // 'name'          => [
            //     'filled',
            //     Rule::unique($table)->ignore($id)->where(function ($query) {
            //         $query->where('deleted_at', null);
            //     })
            // ],
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