<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsSessionLogs;

// 配置
use App\Base\Config\BaseConfig;

// use App\Http\Config\AssetFileConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Http\Fresns\FresnsFiles\FresnsFilesConfig;

class AmConfig extends BaseConfig
{
    // 主表
    const CFG_TABLE = 'session_logs';

    //记录行为结果0-未知或执行中1-失败2-成功
    const OBJECT_RESULT_DEFAULT = 0;
    const OBJECT_RESULT_ERROR = 1;
    const OBJECT_RESULT_SUCCESS = 2;

    //记录类型
    const OBJECT_TYPE_USER_LOGIN = 3;
    const OBJECT_TYPE_MEMBER_LOGIN = 7;
    const OBJECT_TYPE_PLUGIN = 15;

    // 主表额外搜索字段
    const ADDED_SEARCHABLE_FIELDS = [

    ];

    const SESSION_OBJECT_TYPE_ARR = [
        '未知' => 1,
        '注册' => 2,
        '登录' => 3,
        '注销' => 4,
        '重置用户密码' => 5,
        '修改用户资料' => 6,
        '成员登录' => 7,
        '修改成员资料' => 8,
        '交易支出' => 9,
        '交易收入' => 10,
        '创建帖子草稿' => 11,
        '创建评论草稿' => 12,
        '发表帖子内容' => 13,
        '发表评论内容' => 14,
        '定时任务' => 15,
    ];

    // model 使用 - 表单映射
    const FORM_FIELDS_MAP = [
        'id' => 'id',
        'name' => 'name',
        'nickname' => 'nickname',
        'rank_num' => 'rank_num',
        'is_enable' => 'is_enable',
        'remark' => 'remark',
        'type' => 'type',
        'file_id' => 'file_id',
        'file_url' => 'file_url',
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