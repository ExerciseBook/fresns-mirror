<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use App\Base\Config\BaseConfig;
use App\Base\Models\BaseModel;
use App\Http\Share\Common\LogService;
use Doctrine\DBAL\Driver\IBMDB2\DB2Connection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DBHelper
{
    // Multiple tables, Check if there is a table
    public static function hasTable($tableName)
    {
        $inMysql = Schema::connection(BaseConfig::MYSQL_CONNECTION)->hasTable($tableName);
        $inMysqlHelper = Schema::connection(BaseConfig::MYSQL_CONNECTION_HELPER)->hasTable($tableName);

        return $inMysql || $inMysqlHelper;
    }

    // Multiple tables, Check if there is a table
    public static function hasTableInCurrentDB($tableName)
    {
        $inMysql = Schema::connection(BaseConfig::MYSQL_CONNECTION)->hasTable($tableName);

        return $inMysql;
    }

    // Multiple tables, Get connection
    public static function getConnectionName($tableName)
    {
        $inMysqlHelper = Schema::connection(BaseConfig::MYSQL_CONNECTION_HELPER)->hasTable($tableName);
        if ($inMysqlHelper) {
            return BaseConfig::MYSQL_CONNECTION_HELPER;
        }

        return BaseConfig::MYSQL_CONNECTION;
    }

    public static function compareDb($conn1, $conn2)
    {

        // Get tables
        $table1Arr = self::getAllTables($conn1);
        $table2Arr = self::getAllTables($conn2);

        // Comparison of difference tables
        $diffTables1 = array_diff($table1Arr, $table2Arr);
        $diffTables2 = array_diff($table2Arr, $table1Arr);
        $diffTableArr = array_merge($diffTables1, $diffTables2);

        // Comparison of fields in the same table
        $diffTableFieldArr = [];
        $sameTableArr = array_intersect($table1Arr, $table2Arr);

        foreach ($sameTableArr as $table) {
            $table = env('DB_PREFIX').$table;

            $conn1TableColumnMap = self::getTableColumnInfoMap($conn1, $table);
            $conn2TableColumnMap = self::getTableColumnInfoMap($conn2, $table);

            $result = self::compare2TableColumnMapInfo($conn1TableColumnMap, $conn2TableColumnMap);

            LogService::info('conn1TableColumnMap: ', $conn1TableColumnMap);
            LogService::info('conn2TableColumnMap: ', $conn2TableColumnMap);

            if (! empty($result)) {
                $diffTableFieldArr[$table] = $result;
            }
        }

        $data = [];
        $data['diff_tables'] = $diffTableArr;
        $data['diff_table_fields'] = $diffTableFieldArr;

        return $data;
    }

    public static function getAllTables($conn)
    {
        $sql = 'SHOW TABLES';
        $tablesResult = DB::connection($conn)->select($sql);

        $dbName = DB::connection($conn)->getDatabaseName();

        $tables = array_column($tablesResult, "Tables_in_{$dbName}");

        $tableArr = [];
        foreach ($tables as $tableName) {
            $tableArr[] = str_replace(env('DB_PREFIX'), '', $tableName);
        }

        return $tableArr;
    }

    public static function getTableColumns($conn, $table)
    {
        $sql = "SELECT * FROM information_schema.COLUMNS WHERE TABLE_NAME = '{$table}'";
        $queryResult = DB::connection($conn)->select($sql);

        return $queryResult;
    }

    public static function getTableColumnInfoMap($conn, $table)
    {
        $sql = "SELECT * FROM information_schema.COLUMNS WHERE TABLE_NAME = '{$table}'";
        $queryResult = DB::connection($conn)->select($sql);
        $columnNameMap = [];
        foreach ($queryResult as $columnInfo) {
            $columnName = $columnInfo->COLUMN_NAME;
            $columnDataType = $columnInfo->DATA_TYPE;
            $columnNameMap[$columnName] = $columnDataType;
        }

        return $columnNameMap;
    }

    // Compare information from two tables
    public static function compare2TableColumnMapInfo($map1, $map2)
    {
        $diffArr = [];
        foreach ($map1 as $columnName => $columnType) {
            $columnType1 = $columnType;
            $columnType2 = $map2[$columnName] ?? '';

            $compareStr = "{$columnName} : {$columnType1} - {$columnType2}";
            // LogService::info($compareStr);
            if ($columnType1 != $columnType2) {
                $item = [];
                $item['column'] = $columnName;
                $item['columnType1'] = $columnType1;
                $item['columnType2'] = $columnType2;
                $item['remark'] = $compareStr;
                $diffArr[] = $item;
            }
        }

        foreach ($map2 as $columnName => $columnType) {
            $columnType1 = $map1[$columnName] ?? '';
            $columnType2 = $columnType;

            $compareStr = "{$columnName} : {$columnType1} - {$columnType2}";
            // LogService::info($compareStr);
            if ($columnType1 != $columnType2) {
                $item = [];
                $item['column'] = $columnName;
                $item['columnType1'] = $columnType1;
                $item['columnType2'] = $columnType2;
                $item['remark'] = $compareStr;
                $diffArr[] = $item;
            }
        }

        return $diffArr;
    }

    // 批量更新
    public static function updateBatch($tableName, $multipleData = [])
    {
        try {
            if (empty($multipleData)) {
                throw new \Exception('数据不能为空');
            }

            $firstRow = current($multipleData);
            $updateColumn = array_keys($firstRow);

            // 默认以id为条件更新，如果没有ID则以第一个字段为条件
            $referenceColumn = isset($firstRow['id']) ? 'id' : current($updateColumn);
            unset($updateColumn[0]);

            // 拼接sql语句
            $updateSql = 'UPDATE '.$tableName.' SET ';
            $sets = [];
            $bindings = [];

            foreach ($updateColumn as $uColumn) {
                $setSql = '`'.$uColumn.'` = CASE ';
                foreach ($multipleData as $data) {
                    $setSql .= 'WHEN `'.$referenceColumn.'` = ? THEN ? ';
                    $bindings[] = $data[$referenceColumn];
                    $bindings[] = $data[$uColumn];
                }
                $setSql .= 'ELSE `'.$uColumn.'` END ';
                $sets[] = $setSql;
            }

            $updateSql .= implode(', ', $sets);
            $whereIn = collect($multipleData)->pluck($referenceColumn)->values()->all();
            $bindings = array_merge($bindings, $whereIn);
            $whereIn = rtrim(str_repeat('?,', count($whereIn)), ',');
            $updateSql = rtrim($updateSql, ', ').' WHERE `'.$referenceColumn.'` IN ('.$whereIn.')';

            // 传入预处理sql语句和对应绑定数据
            $ret = DB::update($updateSql, $bindings);

            return $ret;
        } catch (\Exception $e) {
            return false;
        }
    }
}
