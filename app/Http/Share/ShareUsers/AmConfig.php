<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */


namespace App\Http\Share\ShareUsers;

// 配置
use App\Base\Config\BaseConfig;
use App\Http\Config\AssetFileConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Http\Fresns\FresnsFiles\FresnsFilesConfig;
class AmConfig extends BaseConfig
{
    // 主表
    CONST CFG_TABLE = 'users';

    CONST USER_TYPE_USER = 2;

    CONST USER_TYPE_MERCHANT = 8;

    
    // 主表额外搜索字段
    CONST ADDED_SEARCHABLE_FIELDS = [
        'login_name'    => ['field' => 'login_name',    'op'   => 'LIKE'],
        'phone'    => ['field' => 'phone',    'op'   => 'LIKE'],
        'email'    => ['field' => 'email',    'op'   => 'LIKE'],
        'type'    => ['field' => 'type',    'op'   => '='],
        'id'    => ['field' => 'id',    'op'   => '='],
        'apply_status'    => ['field' => 'apply_status',    'op'   => '='],
        'user_type'    => ['field' => 'user_type',    'op'   => '='],
    ];

    CONST APPEND_SEARCHABLE_FIELDS = [
        'user_name'    => ['field' => 'nickname',    'op'   => 'LIKE'],
        'customer_name'    => ['field' => 'nickname',    'op'   => 'LIKE'],

    ];

    // model 使用 - 表单映射
    CONST FORM_FIELDS_MAP = [
        'id'        => 'id',
        'rank_num'  => 'rank_num',
        'is_enable' => 'is_enable',
        'remark'    => 'remark',
        'type'      => 'type',
        'file_id'   => 'file_id',
        'file_url'  => 'file_url',
        'more_json'  => 'more_json',
        'login_name'    => 'login_name',
        'nickname'    => 'nickname',
        'password'    => 'password',
        'phone'    => 'phone',
        'email'    => 'email',
        'user_type' => 'user_type',
    ];


    // 新增规则
    public function storeRule(){
        $table = self::CFG_TABLE;
        $assetFileTable = FresnsFilesConfig::CFG_TABLE;

        $rule = [
            'login_name'          => [
                'required',
                Rule::unique($table)->where(function ($query) {
                    $query->where('deleted_at', null);
                })
            ],
            'password'          => 'required|min:6',
            'nickname'      => [
                'filled',
                Rule::unique($table)->where(function ($query) {
                    $query->where('deleted_at', null);
                })
            ],
            'phone'      =>
                Rule::unique($table)->where(function ($query) {
                    $query->where('deleted_at', null);
                }),
            'email'      => [
                'email',
                Rule::unique($table)->where(function ($query) {
                    $query->where('deleted_at', null);
                })
            ],
            'file_id'   => ['required',Rule::exists($assetFileTable,'id')->where(function ($query) {
                $query->where('deleted_at', null);
            })],
            'file_url'  => "required|url",
            'more_json' => "json",
        ];

        return $rule;
    }

    // 更新规则
    public function updateRule(){
        $id = request()->input('id');
        $table = self::CFG_TABLE;

        $rule = [
            'id'    => [
                'required',
                Rule::exists($table)->where(function ($query) {
                    $query->where('deleted_at', null);
                })
            ],
            'login_name'          => [
                'filled',
                Rule::unique($table)->ignore($id)->where(function ($query) {
                    $query->where('deleted_at', null);
                })
            ],
//            'nickname'          => [
//                'filled',
//                Rule::unique($table)->ignore($id)->where(function ($query) {
//                    $query->where('deleted_at', null);
//                })
//            ],
            'phone'      =>
                Rule::unique($table)->ignore($id)->where(function ($query) {
                    $query->where('deleted_at', null);
                }),
            // 'email'      => [
            //     'email',
            //     Rule::unique($table)->ignore($id)->where(function ($query) {
            //         $query->where('deleted_at', null);
            //     })
            // ],
            'rank_num'      => 'numeric',
            'file_url'  => "url",
            'more_json' => "json",
        ];

        return $rule;
    }



}
