<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Base\Models;

use App\Base\Config\BaseConfig;
use App\Traits\HookModelTrait;
use App\Traits\QueryTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Http\Fresns\FresnsCmds\FresnsSubPluginConfig;
use App\Http\Fresns\FresnsCmds\FresnsSubPlugin;
use App\Http\Center\Helper\PluginRpcHelper;
use App\Http\Share\Common\LogService;
class BaseModel extends Model
{
    use SoftDeletes;
    use HookModelTrait;

    // 我的数据库链接
    protected $myConnection = BaseConfig::MYSQL_CONNECTION;

    protected $dates = ['deleted_at'];

    public $useCache = false;
    public $pageSize = BaseConfig::DEFAULT_PAGE_SIZE;
    protected $config = null;
    public $hasDeletedAt = true;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->initConnection();
        $this->hookModelInit();
    }

    // 设置 connection
    protected function initConnection()
    {
        DB::setDefaultConnection($this->myConnection);
    }

    // 可搜索字段
    public function initSearchableFields()
    {
        $searchableFields = BaseConfig::DEFAULT_SEARCHABLE_FIELDS;
        $searchableFields = array_merge($searchableFields, $this->getAddedSearchableFields());
        $searchableFields = array_merge($searchableFields, $this->getAppendSearchableFields());
        return $searchableFields;
    }

    // 获取新增的搜索字段
    public function getAddedSearchableFields()
    {
        return [];
    }

    // 获取附加表的搜索字段
    public function getAppendSearchableFields()
    {
        return [];
    }

    // 获取join 表的搜索字段, 仅在dbQuery时候使用
    public function getJoinSearchableFields()
    {
        return [];
    }

    // 搜索排序字段
    public function initOrderByFields()
    {
        $orderByFields = [
            // 'rank_num' => 'ASC',
            'id' => 'DESC',
            // 'updated_at'    => 'DESC',
        ];
        return $orderByFields;
    }

    // 新增
    public function store($input)
    {
        $id = DB::table($this->table)->insertGetId($input);
        // 新增完了之后的操作
        $this->hookStoreAfter($id);
        // 调用插件订阅命令字
        $cmd = FresnsSubPluginConfig::PLG_CMD_SUB_ADD_TABLE;
        $input = [
            'tableName' => $this->table,
            'insertId' => $id,
        ];
        LogService::info('table_input',$input);
        // dd($input);
        PluginRpcHelper::call(FresnsSubPlugin::class, $cmd, $input);
        return $id;
    }

    // 批量新增
    public function batchStore($inputArr)
    {
        $rs = DB::table($this->table)->insert($inputArr);
        return $rs;
    }

    // 数据存在则更新，不存在则写入
    public function updateOrInsertByCond($cond, $input = [])
    {
        return self::updateOrInsert($cond, $input);
    }

    // 更新
    public function updateItem($id, $upInput)
    {

        self::where('id', $id)->update($upInput);
        //   $this->hookUpdateAfter($id);
    }

    // 更新后的操作, 如更新附表, 计算属性等
    public function updateItemAfter($id)
    {

        if (in_array('hookUpdateAfter', get_class_methods($this))) {
            $this->hookUpdateAfter($id);
        }

    }

    // 已存在则更新，不存在则创建
    public static function updateOrCreateItem($cond, $input)
    {
        $c = get_called_class();
        $m = new $c;
        $m->updateOrCreate($cond, $input);
        return true;
    }

    // 批量删除
    public function destroyByIdArr($idArr)
    {
        if ($this->canDelete($idArr)) {
            $this->hookDestroyBefore($idArr);
            self::whereIn('id', $idArr)->delete();
            $this->updateDestroyUserId($idArr);
        }
    }

    // 软删除恢复
    public static function restoreItem($id){
        self::withTrashed()->find($id)->restore();
    }

    // 恢复多个
    public static function batchRestore($cond){
        self::withTrashed()->where($cond)->restore();
    }

    // 强制删除
    public static function forceDeleteItem($id){
        self::withTrashed()->find($id)->forceDelete();
    }


    // 查询-单个
    public static function find($id)
    {
        $c = get_called_class();
        $m = new $c;
        return $m->findById($id);
    }

    //
    public static function staticFindByField($field, $value)
    {
        $c = get_called_class();
        $m = new $c;
        return $m->findByField($field, $value);
    }

    // 查询附表记录
    public static function findAppend($field, $value)
    {
        $c = get_called_class();
        $m = new $c;
        return $m->findByField($field, $value);
    }

    // 查询附表记录, 根据条件查询
    public static function findAppendByCond($cond)
    {
        $c = get_called_class();
        $m = new $c;
        return $m->findByCond($cond);
    }

    // 查询-单个
    public function findById($id)
    {
        return self::where("id", $id)->first();
    }

    // 查询-单个-字段条件
    public function findByField($fieldName, $fieldValue)
    {
        return self::where($fieldName, $fieldValue)->first();
    }

    // 查询-单个-条件
    public function findByCond($cond)
    {
        return self::where($cond)->first();
    }

    // 查询-单个-条件-静态
    public static function staticFindByNickname($nickname)
    {
        return self::where('nickname', $nickname)->first();
    }

    // 查询字段值，如根据id获取name
    public static function findValueById($id, $field = 'name')
    {
        return self::where('id', $id)->value($field);
    }

    // 查询字段数组，如根据标签idArr，获取标签名称数组
    public static function getValueArrByIdArr($idArr, $field = 'name')
    {
        return self::whereIn('id', $idArr)->pluck($field);
    }

    //通过where条件查询并返回数组
    public static function getValueArrByCond($cond, $field = 'name')
    {
        return self::where($cond)->pluck($field)->toArray();
    }

    //通过whereIn条件查询并返回数组
    public static function getValueArrByCondIn($key = 'id', $valueArr, $field = 'name')
    {
        return self::whereIn($key, $valueArr)->pluck($field)->toArray();
    }

    // 查询-多个
    public function getByCond($cond = [])
    {
        return self::where($cond)->get();
    }

    // 查询-多个
    public static function getByStaticWithCond($cond = [], $column = ['*'])
    {
        $c = get_called_class();
        $m = new $c;
        return self::where($cond)->get($column);
    }

    public static function getByStaticWithCondArr($cond = [], $column = ['*'])
    {
        $c = get_called_class();
        $m = new $c;
        return self::where($cond)->orderBy('rank_num')->get($column)->toArray();
    }

    // 查询-多个-指定列
    public function getByCondWithFields($cond, $fields)
    {
        return self::where($cond)->select($fields)->get();
    }

    public static function staticGetByCondKVMap($k = 'id', $v = 'name', $cond = [])
    {
        $c = get_called_class();
        $m = new $c;
        return $m->getByCondKVMap($k, $v, $cond);
    }

    // map : 根据条件组合列
    public function getByCondKVMap($k = 'id', $v = 'name', $cond = [])
    {
        return DB::table($this->table)->where($cond)->whereNull('deleted_at')->pluck($v, $k);
    }

    //多条件搜索
    public static function getMultipleCond($map, $column = ['*'])
    {

        $query = self::query();
        foreach ($map as $dataArr) {

            $condition = key($dataArr);
            $field = key($dataArr[$condition]);
            $value = current($dataArr[$condition]);

            switch (strtoupper($condition)) {
                case '=':
                    $query = $query->where($field, $value);
                    break;
                case 'LIKE':
                    $query = $query->whereRaw($field . ' LIKE ?', ["%" . $value . "%"]);
                    break;
                case 'IN' :
                    if (!is_array($value)) {
                        $value = explode(',', $value);
                    }
                    $query = $query->whereIn($field, $value);
                    break;
                case '>=' :
                    $query = $query->where($field, '>=', $value);
                    break;
                case '<=' :
                    $query = $query->where($field, '<=', $value);
                    break;
            }
        }

        return $query->get($column);
    }

    //多条件搜索 分页数据
    public static function getMultipleCondPage($map, $page_param, $column = ['*'])
    {

        $query = self::query();
        foreach ($map as $dataArr) {

            $condition = key($dataArr);
            $field = key($dataArr[$condition]);
            $value = current($dataArr[$condition]);

            switch (strtoupper($condition)) {
                case '=':
                    $query = $query->where($field, $value);
                    break;
                case 'LIKE':
                    $query = $query->whereRaw($field . ' LIKE ?', ["%" . $value . "%"]);
                    break;
                case 'IN' :
                    if (!is_array($value)) {
                        $value = explode(',', $value);
                    }
                    $query = $query->whereIn($field, $value);
                    break;
                case '>=' :
                    $query = $query->where($field, '>=', $value);
                    break;
                case '<=' :
                    $query = $query->where($field, '<=', $value);
                    break;
                case '!=' :
                    $query = $query->where($field, '!=', $value);
                    break;
            }
        }
        request()->offsetSet('page', $page_param['page']);

        $query->orderByDesc('id');
        $result = $query->paginate($page_param['limit'])->toArray();

        $pageInfo['total'] = $result['total'];
        $pageInfo['current'] = $result['current_page'];
        $pageInfo['pageSize'] = $result['per_page'];

        $data = [
            'list' => $result['data'],
            'pagination' => $pageInfo,
        ];
        return $data;
    }

    // 批量更新
    public function updateBatch($multipleData = [])
    {
        try {
            if (empty($multipleData)) {
                throw new \Exception("数据不能为空");
            }

            $tableName = DB::getTablePrefix() . $this->getTable(); // 表名
            $firstRow = current($multipleData);
            $updateColumn = array_keys($firstRow);

            // 默认以id为条件更新，如果没有ID则以第一个字段为条件
            $referenceColumn = isset($firstRow['id']) ? 'id' : current($updateColumn);
            unset($updateColumn[0]);

            // 拼接sql语句
            $updateSql = "UPDATE " . $tableName . " SET ";
            $sets = [];
            $bindings = [];

            foreach ($updateColumn as $uColumn) {
                $setSql = "`" . $uColumn . "` = CASE ";
                foreach ($multipleData as $data) {
                    $setSql .= "WHEN `" . $referenceColumn . "` = ? THEN ? ";
                    $bindings[] = $data[$referenceColumn];
                    $bindings[] = $data[$uColumn];
                }
                $setSql .= "ELSE `" . $uColumn . "` END ";
                $sets[] = $setSql;
            }

            $updateSql .= implode(', ', $sets);
            $whereIn = collect($multipleData)->pluck($referenceColumn)->values()->all();
            $bindings = array_merge($bindings, $whereIn);
            $whereIn = rtrim(str_repeat('?,', count($whereIn)), ',');
            $updateSql = rtrim($updateSql, ", ") . " WHERE `" . $referenceColumn . "` IN (" . $whereIn . ")";

            // 传入预处理sql语句和对应绑定数据
            $ret = DB::update($updateSql, $bindings);
            return $ret;
        } catch (\Exception $e) {
            return false;
        }
    }

    // 静态方法
    public static function staticBuildSelectOptions($key = 'id', $text = 'name', $cond = [],$price = 'price')
    {
        $c = get_called_class();
        $m = new $c;
        return $m->buildSelectOptions($key, $text, $cond,$price);
    }

    // 组件下拉框选择
    public function buildSelectOptions($key = 'id', $text = 'name', $cond = [],$price = 'price')
    {
        if (Schema::hasColumn('users', 'rank_num')){
            $items = self::where($cond)->orderBy('rank_num', 'ASC')->get();
        } else {
            $items = self::where($cond)->get();

        }
        $newItemArr = [];
        foreach ($items as $item) {
            $it = [];
            $it['key'] = $item->$key;
            $it['text'] = $item->$text;
            if (!empty($item->login_name)) $it['text'] .= '[' . $item->login_name . ']';
            //$it['login_name']  = $item->login_name;
            //   $it['name']  = $item->name;
            if($price == 'show_price'){
                $it['price'] = $item->price_sale;
            }
            $newItemArr[] = $it;
        }
        return $newItemArr;
    }
     // 静态方法
    public static function staticBuildSelectOptions2($key = 'id', $text = 'name', $cond = [],$price = 'price')
    {
        $c = get_called_class();
        $m = new $c;
        return $m->buildSelectOptions2($key, $text, $cond,$price);
    }
    public function buildSelectOptions2($key = 'id', $text = 'name', $cond = [])
    {
        $items = self::where($cond)->get();
        $newItemArr = [];
        foreach ($items as $item) {
            $it = [];
            $it['key'] = $item->$key;
            $it['text'] = $item->$text;
            if (!empty($item->login_name)) $it['text'] .= '[' . $item->login_name . ']';
            //$it['login_name']  = $item->login_name;
            //   $it['name']  = $item->name;
            $newItemArr[] = $it;
        }
        return $newItemArr;
    }
    //组建下拉框数组查询
    public static function getBuildSelectOptions($key = 'id', $text = 'name', $fieldValue, $fieldName = 'id')
    {
        $items = self::whereIn($fieldName, $fieldValue)->orderBy('rank_num', 'ASC')->get();
        $newItemArr = [];
        foreach ($items as $item) {
            $it = [];
            $it['key'] = $item->$key;
            $it['text'] = $item->$text;
            //   $it['name']  = $item->name;
            $newItemArr[] = $it;
        }
        return $newItemArr;
    }

    public static function buildSelectTreeData($key = 'id', $text = 'name', $cond = [])
    {
        $items = self::where($cond)->orderBy('rank_num', 'ASC')->get();
        $newItemArr = [];
        foreach ($items as $item) {
            $it = [];
            $it['key'] = $item->$key;
            $it['value'] = $item->$key;
            $it['name'] = $item->$text;
            $it['title'] = $item->$text;
            $newItemArr[] = $it;
        }
        return $newItemArr;
    }

    public static function buildSelectTreeDataByNoRankNum($key = 'id', $text = 'name', $cond = [])
        {
            $items = self::where($cond)->get();
            $newItemArr = [];
            foreach ($items as $item) {
                $it = [];
                $it['key'] = $item->$key;
                $it['value'] = $item->$key;
                $it['name'] = $item->$text;
                $it['title'] = $item->$text;
                $newItemArr[] = $it;
            }
            return $newItemArr;
        }
    public function buildCheckboxOptions($label = 'name', $value = 'id')
    {
        $items = self::all();
        $newItemArr = [];
        foreach ($items as $item) {
            $it = [];
            $it['label'] = $item->$label;
            $it['value'] = $item->$value;
            $newItemArr[] = $it;
        }
        return $newItemArr;
    }

    // 是否可删除
    public function canDelete($idArr)
    {
        return true;
    }

    // 是否可编辑
    public function canUpdate($idArr)
    {

    }

    public function getDates()
    {
        return $this->dates;
    }

    // 前台表单-数据库表字段映射
    public function formFieldsMap()
    {
        return [];
    }

    //
    public function convertFormRequestToInput()
    {

        $req = request();
        $fieldMap = $this->formFieldsMap();

        foreach ($fieldMap as $inputField => $tbField) {
            if ($req->has($inputField)) {
                $srcValue = $req->input($inputField);
                if ($srcValue == 0 || $srcValue == '0') {
                    $input[$tbField] = $srcValue;
                }


                if ($srcValue === false || !empty($req->input($inputField, ''))) {
                    $input[$tbField] = $req->input($inputField);
                }
            }
        }

        // 创建用户ID create_user_id
        if (Schema::hasColumn($this->table, 'create_user_id')) {
            $user = Auth::user();
            $input['create_user_id'] = $user->id ?? NULL;
        }

        return $input;
    }

    // 更新删除用户ID
    public function updateDestroyUserId($idArr)
    {
        if (Schema::hasColumn($this->table, 'delete_user_id')) {
            $user = Auth::user();
            $up = [];
            $up['delete_user_id'] = $user->id ?? NULL;
            DB::table($this->table)->whereIn('id', $idArr)->update($up);
        }
    }

    // 返回数据表
    public function getTable()
    {
//        $data = Schema::getColumnListing($table);
        return parent::getTable();
    }


    // 返回数据表
    public static function staticGetConnectionName()
    {
        return (new self)->getConnectionName();
    }

    // 刷新，计算item
    public function computeItem($id)
    {

        return $id;
    }

    // 清除表数据
    protected function clearData()
    {

    }

    // 获取原生SQL查询
    public function getRawSqlQuery()
    {
        return true;
    }

    // 获取列信息
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()
            ->getColumnListing($this->getTable());
    }

}