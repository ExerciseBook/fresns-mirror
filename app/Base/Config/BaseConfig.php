<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Base\Config;

use App\Base\Models\BaseModel;
use Illuminate\Validation\Rule;

class BaseConfig
{
    CONST DEFAULT_PAGE_SIZE = 30;
    CONST DEFAULT_LARGE_PAGE_SIZE = 500;
    CONST DEFAULT_ALL_IN_ONE_PAGE_SIZE = 30000;
    CONST DEFAULT_ROOT_ID = 1;
    CONST PHONE_REG="/^1[34578]{1}\d{9}$/";
    CONST DEFAULT_ADMIN_ID = 1;

    CONST RULE_INDEX = 'index';
    CONST RULE_STORE = 'store';
    CONST RULE_UPDATE = 'update';
    CONST RULE_DETAIL = 'detail';
    CONST RULE_DESTROY = 'destroy';
    CONST RULE_STORE_MORE_JSON = 'store_more_json';
    CONST RULE_UPDATE_MORE_JSON = 'update_more_json';
    CONST IMPORT_RULE = [
        'excel' => ['required', 'file', 'mimes:xls,xlsx'],
    ];

    CONST NICKNAME = 'nickname';

    // 查询模式
    CONST QUERY_TYPE_DB_QUERY= 'db_query';  // 支持join配置的查询
    CONST QUERY_TYPE_SQL_QUERY= 'sql_query'; // 原生SQL查询

    // button
    CONST BUTTON_INDEX = 'index_btn';
    CONST BUTTON_CREATE = 'create_btn';
    CONST BUTTON_UPDATE = 'update_btn';
    CONST BUTTON_DELETE = 'delete_btn';
    CONST BUTTON_DETAIL = 'detail_btn';
    CONST BUTTON_IMPORT = 'import_btn';
    CONST BUTTON_EXPORT = 'export_btn';

    // status 1-normal, 2-disabled
    CONST BUTTON_INFO = [
        ['key' => self::BUTTON_INDEX, 'show' => true, 'name' => '列表', 'status' => 'normal'],
        ['key' => self::BUTTON_CREATE, 'show' => true, 'name' => '新建', 'status' => 'normal'],
        ['key' => self::BUTTON_UPDATE, 'show' => true, 'name' => '编辑', 'status' => 'normal'],
        ['key' => self::BUTTON_DELETE, 'show' => true, 'name' => '删除', 'status' => 'normal'],
        ['key' => self::BUTTON_DETAIL, 'show' => true, 'name' => '详情', 'status' => 'normal'],
        ['key' => self::BUTTON_IMPORT, 'show' => false, 'name' => '导入', 'status' => 'normal'],
        ['key' => self::BUTTON_EXPORT, 'show' => false, 'name' => '导出', 'status' => 'normal'],
    ];

    CONST ENABLE_VALUE_ARR = [0, 1];

    //启用
    CONST ENABLE_TRUE = 1;
    // CONST ENABLE_TRUE = true;
    //禁用
    CONST ENABLE_FALSE = 0;
    // CONST ENABLE_FALSE = false;

    CONST ENABLE_OPTION = [
        ['key' => self::ENABLE_TRUE, 'text' => '启用'],
        ['key' => self::ENABLE_FALSE, 'text' => '禁用'],
    ];


    CONST TABLE_AREA = 'area';

    // 数据库链接
    CONST MYSQL_CONNECTION = 'mysql';
    CONST MYSQL_CONNECTION_SLAVE = 'mysql_slave';
    CONST MYSQL_CONNECTION_HELPER = "mysql_helper";

    // 主表额外的查询规则
    CONST ADDED_SEARCHABLE_FIELDS = [];

    // 附表额外的查询规则
    CONST APPEND_SEARCHABLE_FIELDS = [
        'child_begin_at' => ['field' => 'start_at', 'op' => '>='],
        'child_end_at' => ['field' => 'end_at', 'op' => '<='],
    ];

    // join查询规则，部分情况使用
    CONST JOIN_SEARCHABLE_FIELDS = [
    ];

    // 分类数据表的查询规则，只查询一级节点
    protected $treeSearchRule = [];

    // 模型 默认搜索字段
    CONST DEFAULT_SEARCHABLE_FIELDS = [
        'id' => ['field' => 'id', 'op' => '='],
        'ids' => ['field' => 'id', 'op' => 'IN'],
        'name' => ['field' => 'name', 'op' => 'LIKE'],
        'type' => ['field' => 'type', 'op' => 'IN'],
        'status' => ['field' => 'status', 'op' => 'IN'],
        'created_at_from' => ['field' => 'created_at', 'op' => '>='],
        'created_at_to' => ['field' => 'created_at', 'op' => '<='],
        'updated_at_from' => ['field' => 'updated_at', 'op' => '>='],
        'updated_at_to' => ['field' => 'updated_at', 'op' => '<='],
        'nickname' => ['field' => 'nickname', 'op' => 'LIKE'],
        'is_enable' => ['field' => 'is_enable', 'op' => '='],
        'create_user_id' => ['field' => 'create_user_id', 'op' => '='],
    ];

    /**
     * 配置类基本功能
     */

    // 用户类型
    CONST USER_TYPE_ADMIN = 1; //管理员
    CONST USER_TYPE_USER = 2; //用户


    // 性别
    CONST GENDER_UNKNOWN = 0;
    CONST GENDER_MAN = 1;
    CONST GENDER_FEMALE = 2;
    CONST GENDER_MAP = [
        self::GENDER_UNKNOWN => '未知',
        self::GENDER_MAN => '男',
        self::GENDER_FEMALE => '女',
    ];
    // 登录方式
    CONST LOGIN_TYPE_EMAIL = 1;
    CONST LOGIN_TYPE_PHONE = 2;
    CONST LOGIN_TYPE_NAME = 3;
    CONST LOGIN_TYPE = [
        self::LOGIN_TYPE_EMAIL => 'email',
        self::LOGIN_TYPE_PHONE => 'phone',
        self::LOGIN_TYPE_NAME => 'login_name',
    ];

    CONST TEST_SELECT_OPTION = [
        ['key' => 1, 'text' => '选项1'],
        ['key' => 2, 'text' => '选项2'],
        ['key' => 3, 'text' => '选项3'],
    ];

    /*
     * 对应的表字段
     */
    public $table;

    public function __construct($table = '')
    {
        $this->table = $table;
    }

    public function getTable()
    {
        return $this->table;
    }

    // 列表规则
    public function indexRule()
    {

    }

    // 新增规则
    public function storeRule()
    {

    }

    // 更新规则
    public function updateRule()
    {

    }

    // 删除规则
    public function destroyRule()
    {
        $table = $this->table;

        $rule = ['ids' => ["required",
            Rule::exists($table)->where(function ($query) {
                $query->where('deleted_at', null);
            })
        ],];

        return $rule;
    }

    // 详情规则
    public function detailRule()
    {
        $table = $this->table;
        $rule = ['id' => ["required",
            Rule::exists($table)->where(function ($query) {
                $query->where('deleted_at', null);
            })
        ],];
        return $rule;
    }

    // 获取tree查询规则
    public function getTreeSearchRule()
    {
        return $this->treeSearchRule;
    }

    // 新建more_json规则
    public function storeMoreJsonRule()
    {
        return [];
    }

    // 更新more_json规则
    public function updateMoreJsonRule()
    {
        return [];
    }

}