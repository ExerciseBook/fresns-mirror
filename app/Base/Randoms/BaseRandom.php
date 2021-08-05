<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Base\Randoms;

// 随机数据
use App\Base\Models\BaseModel;
use App\Helpers\DBHelper;
use App\Helpers\RandomHelper;
use App\Helpers\SearchHelper;
use App\Http\Share\Common\LogService;
use App\Plugins\Crm\Models\CrmUser;
use App\Traits\RandomTrait;
use Doctrine\DBAL\Query\QueryBuilder;
use Faker\Generator;
use Illuminate\Support\Facades\DB;

class BaseRandom
{
    // 表
    protected $table;

    // 连接对象
    protected $conn;

    // faker
    public $faker;

    // 列表默认随机5条
    CONST RAND_DEFAULT_COUNT = 5;
    protected $randCount = self::RAND_DEFAULT_COUNT;

    // 生成数据 前置表, 即生成数据前，判断是否有其他表数据需要生成。
    protected $genBeforeTables = [];

    // 清空数据 前置表, 即删除数据前，判断是否有其他表数据需要删除。
    protected $clearBeforeTables = [];

    // 默认字段
    protected $defaultRandomMap = [
        'name'      => RandomHelper::RAND_NAME,
        'remark'    => RandomHelper::RAND_REMARK,
        'more_json' => RandomHelper::RAND_JSON,
        'file_url'  => RandomHelper::RAND_FILE,
        'rank_num'  => RandomHelper::RAND_INT,
        'nickname'  => RandomHelper::RAND_STRING,
        'is_enable' => RandomHelper::RAND_BOOL,
    ];

    // 当前表的增加字段
    protected $addedRandomMap = [

    ];

    public function __construct()
    {
//        $this->conn = DBHelper::getConnectionName($this->table);
//        DB::setDefaultConnection($this->conn);
    }


    public function getTable(){
        return $this->table;
    }

    public function getRandCount(){
        return $this->randCount;
    }

    public function setFaker($faker){
        $this->faker = $faker;
    }
    public function getFaker():Generator{
        return $this->faker;
    }

    // 生成数据之前进行检查, 比如检查关联表是否有数据等， 默认false
    protected function canGenRandomData():bool {
        foreach ($this->genBeforeTables as $genBeforeTable){
            $c = DB::connection($this->conn)->table($genBeforeTable)->count();
            if($c == 0){
                LogService::warning("generate table not ready: ", $genBeforeTable);
                return false;
            }
        }
        return true;
    }

    // 清空数据之前进行检查, 比如检查关联表是否有数据等， 默认false
    protected function canClearData():bool {
        foreach ($this->clearBeforeTables as $clearBeforeTable){
            $c = DB::connection($this->conn)->table($clearBeforeTable)->count();
            if($c > 0){
                LogService::warning("clear tables not ready: ", $clearBeforeTable);
                return false;
            }
        }

        return true;
    }

    // 根据表的列名过滤生成的字段
    protected function filterGenData($dataMap){

        $m = new BaseModel();
        $m->setTable($this->table);
        $m->setConnection($this->conn);
        $tableColumns = $m->getTableColumns();
        foreach ($dataMap as $field => $randType){
            if(!in_array($field, $tableColumns)){
                unset($dataMap[$field]);
            }
        }

        return $dataMap;
    }

    // 生成
    public function generate($toDB = false, int $count = 0){

        // 检查前置表
        if(!$this->canGenRandomData()){
            LogService::warning("数据生成检查异常: ", "generate check before table fail");
            return false;
        }

        // 生成的数量
        $genCount = $count > 0 ? $count : $this->randCount;

        // 生成字段
        $randMap  = array_merge($this->defaultRandomMap, $this->addedRandomMap);

        $randDataArr = [];

        // 设置字段
        $randomHelper = new RandomHelper();
        $this->setFaker($randomHelper->getFaker());

        // 生成
        for($i = 0; $i < $genCount; $i++){
            // 默认字段

            $item = [];
            foreach ($randMap as $field => $randMethod){
             //   dd($randMethod);
                $item[$field] = $randomHelper->$randMethod();
            }

            // 特殊字段, 如关联字段
            $specItem = $this->genSpecialRandData($randomHelper->getFaker());

            // 合并
            $randDataArr[] = $this->filterGenData(array_merge($item, $specItem));
        }

        // 插入数据库
        if($toDB){
            DB::connection($this->conn)->table($this->table)->insert($randDataArr);
        }

        return $randDataArr;
    }

    // 生成特殊字段, 如关联字段
    protected function  genSpecialRandData(Generator $faker){
        return [];
    }

    // 清空数据
    public function clearData(){
        if($this->canClearData()){
            $beginAt = request()->input('begin_at');
            $endAt = request()->input('end_at');
            DB::connection($this->conn)->table($this->table)->where('created_at', '>=', $beginAt)
                ->where('created_at', '<=', $endAt)->delete();
        }
        return false;
    }

    // 根据条件从表中随机获取一个字段
    protected function randElementInTable($table, $cond = [], $field = 'id'){

        $dataArr = SearchHelper::doSimpleQuery($table, $cond);

        if(empty($dataArr)){
            LogService::warning("获取关联字段为空", $table);
            return false;
        }
        $fieldArr = array_column($dataArr, $field);
        $faker = $this->getFaker();

        $id = $faker->randomElement($fieldArr);
        return $id;
    }

}
