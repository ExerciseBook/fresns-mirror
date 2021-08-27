<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Base\Randoms;

use App\Base\Models\BaseModel;
use App\Helpers\RandomHelper;
use App\Helpers\SearchHelper;
use App\Http\Share\Common\LogService;
use Faker\Generator;
use Illuminate\Support\Facades\DB;

class BaseRandom
{
    // Table
    protected $table;

    // Connected objects
    protected $conn;

    // Faker
    public $faker;

    // List of 5 random items by default
    const RAND_DEFAULT_COUNT = 5;
    protected $randCount = self::RAND_DEFAULT_COUNT;

    // Generate Data Preceding Table.
    // That is, before generating data, determine whether there is other table data to be generated.
    protected $genBeforeTables = [];

    // Empty Data Preceding Table.
    // That is, before deleting data, determine whether there is other table data to be deleted.
    protected $clearBeforeTables = [];

    // Default Column
    protected $defaultRandomMap = [
        'name'      => RandomHelper::RAND_NAME,
        'remark'    => RandomHelper::RAND_REMARK,
        'more_json' => RandomHelper::RAND_JSON,
        'file_url'  => RandomHelper::RAND_FILE,
        'rank_num'  => RandomHelper::RAND_INT,
        'nickname'  => RandomHelper::RAND_STRING,
        'is_enable' => RandomHelper::RAND_BOOL,
    ];

    // Adding fields to the current table
    protected $addedRandomMap = [

    ];

    public function __construct()
    {
        // $this->conn = DBHelper::getConnectionName($this->table);
        // DB::setDefaultConnection($this->conn);
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getRandCount()
    {
        return $this->randCount;
    }

    public function setFaker($faker)
    {
        $this->faker = $faker;
    }

    public function getFaker(): Generator
    {
        return $this->faker;
    }

    // Check before generating data, e.g. check if there is data in the associated table, etc.
    // default is false
    protected function canGenRandomData(): bool
    {
        foreach ($this->genBeforeTables as $genBeforeTable) {
            $c = DB::connection($this->conn)->table($genBeforeTable)->count();
            if ($c == 0) {
                LogService::warning('generate table not ready: ', $genBeforeTable);

                return false;
            }
        }

        return true;
    }

    // Check before clearing the data, e.g. check if there is data in the associated table, etc.
    // default is false
    protected function canClearData(): bool
    {
        foreach ($this->clearBeforeTables as $clearBeforeTable) {
            $c = DB::connection($this->conn)->table($clearBeforeTable)->count();
            if ($c > 0) {
                LogService::warning('clear tables not ready: ', $clearBeforeTable);

                return false;
            }
        }

        return true;
    }

    // Fields generated by filtering based on the column names of the table
    protected function filterGenData($dataMap)
    {
        $m = new BaseModel();
        $m->setTable($this->table);
        $m->setConnection($this->conn);
        $tableColumns = $m->getTableColumns();
        foreach ($dataMap as $field => $randType) {
            if (! in_array($field, $tableColumns)) {
                unset($dataMap[$field]);
            }
        }

        return $dataMap;
    }

    // Generate
    public function generate($toDB = false, int $count = 0)
    {

        // Check front table
        if (! $this->canGenRandomData()) {
            LogService::warning('Data generation check exception: ', 'generate check before table fail');

            return false;
        }

        // Number of Generated
        $genCount = $count > 0 ? $count : $this->randCount;

        // Generate columns
        $randMap = array_merge($this->defaultRandomMap, $this->addedRandomMap);

        $randDataArr = [];

        // Set columns
        $randomHelper = new RandomHelper();
        $this->setFaker($randomHelper->getFaker());

        // Generate
        for ($i = 0; $i < $genCount; $i++) {
            // Default columns
            $item = [];
            foreach ($randMap as $field => $randMethod) {
                $item[$field] = $randomHelper->$randMethod();
            }

            // Special columns (e.g., associated columns)
            $specItem = $this->genSpecialRandData($randomHelper->getFaker());

            // Merger
            $randDataArr[] = $this->filterGenData(array_merge($item, $specItem));
        }

        // Insert into database
        if ($toDB) {
            DB::connection($this->conn)->table($this->table)->insert($randDataArr);
        }

        return $randDataArr;
    }

    // Generate Special columns (e.g., associated columns)
    protected function genSpecialRandData(Generator $faker)
    {
        return [];
    }

    // Empty data
    public function clearData()
    {
        if ($this->canClearData()) {
            $beginAt = request()->input('begin_at');
            $endAt = request()->input('end_at');
            DB::connection($this->conn)->table($this->table)->where('created_at', '>=', $beginAt)
                ->where('created_at', '<=', $endAt)->delete();
        }

        return false;
    }

    // Get a random column from a table based on a condition
    protected function randElementInTable($table, $cond = [], $field = 'id')
    {
        $dataArr = SearchHelper::doSimpleQuery($table, $cond);

        if (empty($dataArr)) {
            LogService::warning('Get the associated column as empty', $table);

            return false;
        }
        $fieldArr = array_column($dataArr, $field);
        $faker = $this->getFaker();

        $id = $faker->randomElement($fieldArr);

        return $id;
    }
}
