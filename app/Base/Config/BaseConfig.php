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
    const DEFAULT_PAGE_SIZE = 30;
    const DEFAULT_LARGE_PAGE_SIZE = 500;
    const DEFAULT_ALL_IN_ONE_PAGE_SIZE = 30000;
    const DEFAULT_ROOT_ID = 1;
    const PHONE_REG = "/^1[34578]{1}\d{9}$/";
    const DEFAULT_ADMIN_ID = 1;

    const RULE_INDEX = 'index';
    const RULE_STORE = 'store';
    const RULE_UPDATE = 'update';
    const RULE_DETAIL = 'detail';
    const RULE_DESTROY = 'destroy';
    const RULE_STORE_MORE_JSON = 'store_more_json';
    const RULE_UPDATE_MORE_JSON = 'update_more_json';
    const IMPORT_RULE = [
        'excel' => ['required', 'file', 'mimes:xls,xlsx'],
    ];

    const NICKNAME = 'nickname';

    // 查询模式
    const QUERY_TYPE_DB_QUERY = 'db_query';  // 支持join配置的查询
    const QUERY_TYPE_SQL_QUERY = 'sql_query'; // 原生SQL查询

    // button
    const BUTTON_INDEX = 'index_btn';
    const BUTTON_CREATE = 'create_btn';
    const BUTTON_UPDATE = 'update_btn';
    const BUTTON_DELETE = 'delete_btn';
    const BUTTON_DETAIL = 'detail_btn';
    const BUTTON_IMPORT = 'import_btn';
    const BUTTON_EXPORT = 'export_btn';

    // status 1-normal, 2-disabled
    const BUTTON_INFO = [
        ['key' => self::BUTTON_INDEX, 'show' => true, 'name' => '列表', 'status' => 'normal'],
        ['key' => self::BUTTON_CREATE, 'show' => true, 'name' => '新建', 'status' => 'normal'],
        ['key' => self::BUTTON_UPDATE, 'show' => true, 'name' => '编辑', 'status' => 'normal'],
        ['key' => self::BUTTON_DELETE, 'show' => true, 'name' => '删除', 'status' => 'normal'],
        ['key' => self::BUTTON_DETAIL, 'show' => true, 'name' => '详情', 'status' => 'normal'],
        ['key' => self::BUTTON_IMPORT, 'show' => false, 'name' => '导入', 'status' => 'normal'],
        ['key' => self::BUTTON_EXPORT, 'show' => false, 'name' => '导出', 'status' => 'normal'],
    ];

    const ENABLE_VALUE_ARR = [0, 1];

    //启用
    const ENABLE_TRUE = 1;
    // CONST ENABLE_TRUE = true;
    //禁用
    const ENABLE_FALSE = 0;
    // CONST ENABLE_FALSE = false;

    const ENABLE_OPTION = [
        ['key' => self::ENABLE_TRUE, 'text' => '启用'],
        ['key' => self::ENABLE_FALSE, 'text' => '禁用'],
    ];

    const TABLE_AREA = 'area';

    // 数据库链接
    const MYSQL_CONNECTION = 'mysql';
    const MYSQL_CONNECTION_SLAVE = 'mysql_slave';
    const MYSQL_CONNECTION_HELPER = 'mysql_helper';

    // 主表额外的查询规则
    const ADDED_SEARCHABLE_FIELDS = [];

    // 附表额外的查询规则
    const APPEND_SEARCHABLE_FIELDS = [
        'child_begin_at' => ['field' => 'start_at', 'op' => '>='],
        'child_end_at' => ['field' => 'end_at', 'op' => '<='],
    ];

    // join查询规则，部分情况使用
    const JOIN_SEARCHABLE_FIELDS = [
    ];

    // 分类数据表的查询规则，只查询一级节点
    protected $treeSearchRule = [];

    // 模型 默认搜索字段
    const DEFAULT_SEARCHABLE_FIELDS = [
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
     * 配置类基本功能.
     */

    // 用户类型
    const USER_TYPE_ADMIN = 1; //管理员
    const USER_TYPE_USER = 2; //用户

    // 性别
    const GENDER_UNKNOWN = 0;
    const GENDER_MAN = 1;
    const GENDER_FEMALE = 2;
    const GENDER_MAP = [
        self::GENDER_UNKNOWN => '未知',
        self::GENDER_MAN => '男',
        self::GENDER_FEMALE => '女',
    ];
    // 登录方式
    const LOGIN_TYPE_EMAIL = 1;
    const LOGIN_TYPE_PHONE = 2;
    const LOGIN_TYPE_NAME = 3;
    const LOGIN_TYPE = [
        self::LOGIN_TYPE_EMAIL => 'email',
        self::LOGIN_TYPE_PHONE => 'phone',
        self::LOGIN_TYPE_NAME => 'login_name',
    ];

    const TEST_SELECT_OPTION = [
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

        $rule = ['ids' => ['required',
            Rule::exists($table)->where(function ($query) {
                $query->where('deleted_at', null);
            }),
        ]];

        return $rule;
    }

    // 详情规则
    public function detailRule()
    {
        $table = $this->table;
        $rule = ['id' => ['required',
            Rule::exists($table)->where(function ($query) {
                $query->where('deleted_at', null);
            }),
        ]];

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
