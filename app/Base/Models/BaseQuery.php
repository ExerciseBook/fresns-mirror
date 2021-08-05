<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Base\Models;

use App\Base\Config\BaseConfig;
use App\Http\Share\Common\ValidateService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Share\Common\LogService;

class BaseQuery {

    // 查询字段
    protected $searchableFields;

    protected $joinSearchableFields;

    // 排序字段
    protected $orderByFields;

    // 模型
    protected $model;
    protected $table;
    protected $query;

    // 分页字段
    protected $pageInfo;
    protected $page;
    protected $limit;
    protected $queryTotalCount;

    public function __construct(BaseModel $model,  $opts = [])
    {
        $this->model= $model;
        $this->query = $this->model;
        $this->table = $this->model->getTable();
        $this->page = 1;
        $this->limit = $this->getLimit();
        $this->searchableFields = $this->model->initSearchableFields();
        $this->orderByFields = $this->model->initOrderByFields();
        // dbQuery
        $this->dbQuery = DB::table($this->table);
        $this->joinSearchableFields = $this->model->getJoinSearchableFields();
    }

    // 初始化搜索条件
    public function initWhereCond(){
        $req = request();
//        dd($this->searchableFields);
        foreach ($this->searchableFields as $searchField => $arr){
            // 无key跳过
            if(!$req->has($searchField)){
                continue;
            }

            $searchValue = $req->input($searchField);
            // 无值跳过
            if($searchValue === NULL){
                continue;
            }

            // 验证数组字段
            ValidateService::validParamExist($arr, ['field', 'op']);

            $field = $arr['field'];
            $op = $arr['op'];

            $upperOp = strtoupper($op);

            switch ($upperOp) {
                case '=' :
                    $this->query = $this->query->where($field, $searchValue);
                    break;
                case 'IN' :
                    $inArr = explode(',', $searchValue);
                    $this->query = $this->query->whereIn($field, $inArr);
                    break;
                case '>=' :
                    $this->query = $this->query->where($field, '>=', $searchValue);
                    break;
                case '<=' :
                    $this->query = $this->query->where($field, '<=', $searchValue);
                    break;
                case '<>' :
                    $this->query = $this->query->where($field, '!=', $searchValue);
                    break;
                case 'LIKE' :
                    $this->query = $this->query->where($field , 'LIKE', '%' . $searchValue . '%');
                    break;
                case 'DATE_REGION_BEGIN_AT':
                    $this->query = $this->query->where($field, '>=', $searchValue);
                    break;
                case 'DATE_REGION_END_AT':
                    $this->query = $this->query->where($field, '<=', $searchValue);
                    break;
                case 'JSON_ITEM_ARR':
                    $inArr = explode(',', $searchValue);
                    $fieldAttr = $arr['field_attr'] ?? $field;
                    foreach ($inArr as $idx => $value) {
                        if (empty($value)) {
                            continue;
                        }
                        $value = trim($value);
                        $this->query = $this->query->whereJsonContains($field, [$fieldAttr => intval($value)]);
                        if ($idx > 0) {
                            $this->query = $this->query->whereJsonContains($field, [$fieldAttr => intval($value)], 'OR');
                        }
                    }
                    break;
                case'JSON_NUMBER':
                    $inArr = explode(',', $searchValue);
                    foreach ($inArr as &$value){
                        $value = intval($value);
                    }
              //      App\User::where('meta->skills', 'like',  '%Vue%')->get()
                    $this->query = $this->query->whereJsonContains($field, $inArr);

                    break;
                case 'JOIN':
                    $inArr = explode(',', $field);//特殊处理实现多表联表查询
                    $cnt = 0;$resArr = [];
                    foreach ($inArr as $value){
                        $resArr[$cnt] = $value;
                        $cnt++;
                    }
                    $this->query = $this->query->join($resArr[0],$resArr[1],$resArr[2],$resArr[3]);
                    break;
            }
        }

    //dd($this->query);
    //  dd($this->query->toSql());
    }

    // 初始化排序
    public function initOrderBy(){

        foreach ($this->orderByFields as $orderByField => $orderType){
            $this->query = $this->query->orderBy($orderByField, $orderType);
        }
    }

    // 执行查询
    public function executeQuery(){
        $req = request();
        $this->page  = $req->input("currentPage", 1);
        $this->limit = $req->input("pageSize", $this->getLimit());

        // 初始化查询参数
        $this->initWhereCond();
        $this->initOrderBy();
        $req->offsetSet('page', $this->page);

        $result = $this->query->paginate($this->getLimit(), $this->table.'.*');

        $pagination = $this->setSearchPageInfo($result);

        $ret['result'] = $result;
        $ret['pagination'] = $pagination;
        return $ret;
    }


    // 执行查询所有符合条件的数据
    public function executeQueryAll(){
        $req = request();

        // 初始化查询参数
        $this->initWhereCond();
        $this->initOrderBy();
        $req->offsetSet('page', $this->page);

        $result = $this->query->get();

        return $result;
    }

    //执行查询符合条件的数据，并只返回单个字段
    public function executeQueryField($field){
        $req = request();

        // 初始化查询参数
        $this->initWhereCond();
        $this->initOrderBy();
        $req->offsetSet('page', $this->page);

        $result = $this->query->pluck($this->table.'.'.$field);
        return $result;
    }

    public function getLimit(){
        $req = request();
        $limit = intval($req->input("pageSize",$this->model->pageSize));
        return intval($limit);
    }

    public function getQuery(){
        return $this->query;
    }

    public function setSearchPageInfo(LengthAwarePaginator $searchRes){
        $pageInfo = [];
        $pageInfo['total']      = $searchRes->total();  // 总数
        $pageInfo['current']    = $searchRes->currentPage();    // 当前页
        $pageInfo['pageSize']   = $searchRes->perPage();    // 每页数量
        $pageInfo['lastPage']   = $searchRes->lastPage();   // 最后一页

        if($searchRes->perPage() ==  BaseConfig::DEFAULT_LARGE_PAGE_SIZE){
            $perPage =  BaseConfig::DEFAULT_LARGE_PAGE_SIZE . "";
            $pageInfo['pageSizeOptions']   = [$perPage];
            $pageInfo['hideOnSinglePage']  = true;
        }
        $this->pageInfo = $pageInfo;

        return $pageInfo;
    }


    // 初始化DB搜索条件
    public function initWhereCondForDbQuery(){
        $req = request();
        // join 测试
//        $cjoinItem = [
//            'join_table'    => 'crm_customer_append',   // join表
//            'join_type'     => 'join',   // join类型
//            'join_select_fields'   => [
//                'crm_customer_append.id',
//                'crm_customer_append.customer_code',
//                'crm_customer_append.name',
//            ], // join表select字段
//
//            'join_cond_arr'    => [
//                [
//                    'main_table_field' => 'id',
//                    'join_table_field' => 'customer_id',
//                    'op' => '=',
//                ]
//            ], // join条件
//
//            'join_table_cond_arr'  => [
//                'customer_code'   => ['field' => 'customer_code', 'op'   => 'LIKE'],
//            ],  // join表多个条件
//        ];

        $mainTable = $this->table;
        $selectFields = [
            "{$mainTable}.*",
        ];

        DB::connection()->enableQueryLog();  // 开启QueryLog

        // 初始化主表条件
        $this->buildDbQueryWithSearchCondArr($mainTable, $this->searchableFields);

        // 默认条件
        if($this->model->hasDeletedAt){
            $this->dbQuery->where($mainTable.'.deleted_at', '=', NULL);
        }

        //   dd($joinItem);
        // 此处设置 join 条件
        foreach($this->joinSearchableFields as $joinItem){
            // join表
            $joinTable = $joinItem['join_table'];
            // join 表 select 字段
            $joinSelectFields = $joinItem['join_select_fields'];
            $selectFields = array_merge($selectFields, $joinSelectFields);
            // join 条件
            $joinCondArr = $joinItem['join_cond_arr'];

            // join表 查询条件
            $joinTableCondArr = $joinItem['join_table_cond_arr'];


            $needJoin = $this->buildDbQueryWithSearchCondArr($joinTable, $joinTableCondArr);

            if(!$needJoin){
                continue;
            }

            $this->dbQuery->join($joinTable, function($join) use($mainTable, $joinTable, $joinCondArr){
                foreach ($joinCondArr as $joinCond){
                    $mainTableField = $joinCond['main_table_field'];
                    $joinTableField = $joinCond['join_table_field'];
                    $op = $joinCond['op'];
                    $join->on("{$mainTable}.{$mainTableField}", $op, "{$joinTable}.{$joinTableField}");
                }
            });


        }

        //  dd($this->dbQuery);
    }

    // 给表生成条件
    public function buildDbQueryWithSearchCondArr($table, $searchCondArr){
        // 前缀
        // $dbPrefix = env("DB_PREFIX");
        $dbPrefix = '';
        LogService::info("DB PREFIX [$dbPrefix]");
        $table = Str::startsWith($dbPrefix, $table) ? $table :  $dbPrefix . $table;

        // todo 等待调试
        if($table == 'users'){
            $table = 'bl_users';
        }

        $needJoinArr = [];
        $req = request();
        foreach ($searchCondArr as $searchField => $arr){
            // 无key跳过
            if(!$req->has($searchField)){
                continue;
            }

            $searchValue = $req->input($searchField);

            // 无值跳过
            if($searchValue === NULL){
                continue;
            }

            $needJoinArr[] = $searchValue;

            // 验证数组字段
            ValidateService::validParamExist($arr, ['field', 'op']);

            // 附上表
            $field = $table .  "." . $arr['field'];
            $op = $arr['op'];

            $upperOp = strtoupper($op);

            switch ($upperOp) {
                case '=' :
                    $this->dbQuery = $this->dbQuery->where($field, $searchValue);
                    break;
                case 'IN' :
                    $inArr = explode(',', $searchValue);
                    $this->dbQuery = $this->dbQuery->whereIn($field, $inArr);
                    break;
                case '>=' :
                    $this->dbQuery = $this->dbQuery->where($field, '>=', $searchValue);
                    break;
                case '<=' :
                    $this->dbQuery = $this->dbQuery->where($field, '<=', $searchValue);
                    break;
                case 'LIKE' :
                    $this->dbQuery = $this->dbQuery->whereRaw($field . ' LIKE ?', ["%" . $searchValue . "%"]);
                    break;
                case 'DATE_REGION_BEGIN_AT':
                    $this->dbQuery = $this->dbQuery->where($field, '>=', $searchValue);
                    break;
                case 'DATE_REGION_END_AT':
                    $this->dbQuery = $this->dbQuery->where($field, '<=', $searchValue);
                    break;
            }
        }

        // 大于0 才需要join
        return count($needJoinArr) > 0;
    }


    // 执行查询
    public function executeDbQuery(){
        $req = request();
        $this->page  = $req->input("currentPage", 1);
        $this->limit = $req->input("pageSize", $this->getLimit());

        // 初始化查询参数
        $this->initWhereCondForDbQuery();
        $this->initOrderBy();
        $req->offsetSet('page', $this->page);

        $result = $this->dbQuery->paginate($this->getLimit());
        //   dd($result);

        $pagination = $this->setSearchPageInfo($result);

        $ret['result'] = $result;
        $ret['pagination'] = $pagination;
        return $ret;
    }


    public function executeSqlQuery(){
        $req = request();
        $this->page  = $req->input("currentPage", 1);
        $this->limit = $req->input("pageSize", $this->getLimit());

        $req->offsetSet('page', $this->page);

        DB::connection()->enableQueryLog();  // 开启QueryLog

        $sqlQuery = $this->model->getRawSqlQuery();
        // dd($sqlQuery);

        $result = $sqlQuery->paginate($this->getLimit());
        $pagination = $this->setSearchPageInfo($result);

        $queries = \DB::getQueryLog();

        LogService::info("执行语句: ", $queries);

        $ret['result'] = $result;
        $ret['pagination'] = $pagination;
    //    dd($ret);
        return $ret;
    }
}
