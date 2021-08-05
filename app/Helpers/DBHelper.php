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
    // 多库， 检查是否有table
    public static function hasTable($tableName){

        $inMysql = Schema::connection(BaseConfig::MYSQL_CONNECTION)->hasTable($tableName);
        $inMysqlHelper = Schema::connection(BaseConfig::MYSQL_CONNECTION_HELPER)->hasTable($tableName);

        return $inMysql || $inMysqlHelper;
    }

    // 多库， 检查是否有table
    public static function hasTableInCurrentDB($tableName){
        $inMysql = Schema::connection(BaseConfig::MYSQL_CONNECTION)->hasTable($tableName);

        return $inMysql;
    }


    // 多库， 获取connection
    public static function getConnectionName($tableName){

        $inMysqlHelper = Schema::connection(BaseConfig::MYSQL_CONNECTION_HELPER)->hasTable($tableName);
        if($inMysqlHelper){
            return BaseConfig::MYSQL_CONNECTION_HELPER;
        }

        return BaseConfig::MYSQL_CONNECTION;
    }

    public static function compareDb($conn1, $conn2){

        // 获取数据表
        $table1Arr = self::getAllTables($conn1);
        $table2Arr = self::getAllTables($conn2);

        // 差异表比较
        $diffTables1 = array_diff($table1Arr, $table2Arr);
        $diffTables2 = array_diff($table2Arr, $table1Arr);
        $diffTableArr = array_merge($diffTables1, $diffTables2);

        // 相同表的字段比较
        $diffTableFieldArr = [];
        $sameTableArr = array_intersect($table1Arr, $table2Arr);

        foreach ($sameTableArr as $table){

            $table = env("DB_PREFIX") .  $table;

            $conn1TableColumnMap = self::getTableColumnInfoMap($conn1, $table);
            $conn2TableColumnMap = self::getTableColumnInfoMap($conn2, $table);

            $result = self::compare2TableColumnMapInfo($conn1TableColumnMap, $conn2TableColumnMap);

            LogService::info("conn1TableColumnMap: ", $conn1TableColumnMap);
            LogService::info("conn2TableColumnMap: ", $conn2TableColumnMap);

        //    dd($result);
            if(!empty($result)){
                $diffTableFieldArr[$table] = $result;
            }

        }

        $data = [];
        $data['diff_tables'] = $diffTableArr;
        $data['diff_table_fields'] = $diffTableFieldArr;

        return $data;
    }

    public static function getAllTables($conn){
        $sql = "SHOW TABLES";
        $tablesResult = DB::connection($conn)->select($sql);

        $dbName = DB::connection($conn)->getDatabaseName();

        $tables = array_column($tablesResult, "Tables_in_{$dbName}");

        $tableArr = [];
        foreach ($tables as $tableName){
            $tableArr[] = str_replace(env("DB_PREFIX"), '', $tableName);
        }

        return $tableArr;
    }


    public static function getTableColumns($conn, $table){
        $sql = "SELECT * FROM information_schema.COLUMNS WHERE TABLE_NAME = '{$table}'";
        $queryResult = DB::connection($conn)->select($sql);
        return $queryResult;
    }

    public static function getTableColumnInfoMap($conn, $table){
        $sql = "SELECT * FROM information_schema.COLUMNS WHERE TABLE_NAME = '{$table}'";
        $queryResult = DB::connection($conn)->select($sql);
        $columnNameMap = [];
        foreach ($queryResult as $columnInfo){
            $columnName = $columnInfo->COLUMN_NAME;
            $columnDataType = $columnInfo->DATA_TYPE;
            $columnNameMap[$columnName] = $columnDataType;
        }

        return $columnNameMap;
    }

    // 比较两个表的信息
    public static function compare2TableColumnMapInfo($map1, $map2){

        //
        $diffArr = [];
        foreach ($map1 as $columnName => $columnType){
            $columnType1 = $columnType;
            $columnType2 = $map2[$columnName] ?? '';

            $compareStr = "{$columnName} : {$columnType1} - {$columnType2}";
        //    LogService::info($compareStr);
            if($columnType1 != $columnType2){
                $item = [];
                $item['column'] = $columnName;
                $item['columnType1'] = $columnType1;
                $item['columnType2'] = $columnType2;
                $item['remark'] = $compareStr;
                $diffArr[] = $item;
            }
        }

        foreach ($map2 as $columnName => $columnType){
            $columnType1 = $map1[$columnName] ?? '';
            $columnType2 = $columnType;

            $compareStr = "{$columnName} : {$columnType1} - {$columnType2}";
         //   LogService::info($compareStr);
            if($columnType1 != $columnType2){
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

    public static function tableMarkdown($conn = 'mysql'){
        $totalSqlArr = [];
        if(intval(request()->input('adjust')) == 1){
            $totalSqlArr = self::adjustTableFieldOrder($conn);
        }
     //   dd(123);
        $tableArr = self::getAllTables($conn);

        foreach ($tableArr as $table){
            $realTable = env("DB_PREFIX") .  $table;

            if($table == 'aa_template'){
                $realTable = $table;
               // continue;
            }

           // dd($realTable);

            // 查询表信息
            $sql = "SELECT * FROM information_schema.TABLES WHERE TABLE_NAME = '{$realTable}'";
            $queryResult = DB::connection($conn)->select($sql);
            $tableComment = $queryResult[0]->TABLE_COMMENT;

            // 查询列信息
            $sql = "SELECT * FROM information_schema.COLUMNS WHERE TABLE_NAME = '{$realTable}'";
            $queryResult = DB::connection($conn)->select($sql);

            //  dd($realTable);
          //  dd($queryResult);
            // ### 多媒体表 *media*

            $markdownArr = [
                "### {$tableComment} *{$table}*",
                "|  字段名  |  字段类型  |  字段注释  |  默认值  |  可空  |  备注  |",
                "|  ---  |  ---  |  --- |  ---  |  ---  |  ---  |"
            ];

          //  dd($queryResult);

            foreach ($queryResult as $columnInfo){
                $item = [];
                $commentArr = explode("#", $columnInfo->COLUMN_COMMENT);
                $item[] = $columnInfo->COLUMN_NAME;
              //  $item[] = $columnInfo->DATA_TYPE;
              //  $item[] = $columnInfo->COLUMN_TYPE . "(". $columnInfo->CHARACTER_MAXIMUM_LENGTH . ")";
                $dataType = $columnInfo->DATA_TYPE;
                $columnType = $columnInfo->COLUMN_TYPE;
                if($dataType == 'bigint'){
                    $columnType = "bigint(" . $columnInfo->NUMERIC_PRECISION . ") unsigned";
                }
                if($dataType == 'int'){
                    $columnType = "int(" . $columnInfo->NUMERIC_PRECISION . ") unsigned";
                }
                if($dataType == 'tinyint'){
                    if(!Str::contains($columnType, "(")){
                       // $precision = "tinyint(" . $columnInfo->NUMERIC_PRECISION . ")";
                        $precision = "tinyint(1)";
                        $columnType = str_replace("tinyint", $precision, $columnType);
                    }
                }
                $item[] = $columnType;
                $item[] = $commentArr[0] ?? '';
                $item[] = $columnInfo->COLUMN_DEFAULT;
                $item[] = $columnInfo->IS_NULLABLE;
                $item[] = $commentArr[1] ?? '';

                $str = implode(" | ", $item);
                $str = "| " . $str . " |";
                $markdownArr[] = $str;
            }
            $markdownStrArr[] = implode("\n", $markdownArr);
        }

        $finalStr = implode("\n\n\n", $markdownStrArr);

        // 准备数据

        $d = date("YmdHis", time());
        $fileName = "table_markdown_{$d}.txt";
        $filePath = base_path() . "/storage/app/public/export/{$fileName}";

        // 写入
        file_put_contents($filePath, $finalStr);

        $domain = CommonHelper::domain();
        $url = $domain . "/storage/export/$fileName";
        $info = [];
        $info['file_url'] = $url;
        $info['table_count'] = count($tableArr);
        $info['table_arr'] = $tableArr;
        $info['total_sql_arr'] = $totalSqlArrStr ?? '';
        $info['total_sql_arr_str'] = implode(" ", $totalSqlArr);
        return $info;
    }


    // 调整表格字段顺序
    public static function adjustTableFieldOrder($conn = 'mysql'){
        $tableArr = self::getAllTables($conn);

        $fieldOrderArr = [
            'is_enable',
            'rank_num',
            'remark',
            'file_id',
            'file_url',
            'more_json',
            'create_user_id',
            'delete_user_id',
            'created_at',
            'updated_at',
            'deleted_at',
        ];

        // ALTER TABLE address MODIFY COLUMN city bigint(20);,
        $fieldLengthMap = [
            'id'    => "bigint(20) UNSIGNED AUTO_INCREMENT comment  '自增主键ID'",
            'name'  => "varchar(255) comment '名称'",
            'is_enable'  => "tinyint(1) UNSIGNED DEFAULT '1' comment '0-无效,1-有效'",
            'file_id'    => "int(10) UNSIGNED comment '资源文件ID'",
            'file_url'   => "varchar(255) comment '资源文件URL'",
            'rank_num'   => "int(10) UNSIGNED comment '升序排序1-n'",
            'remark'     => "text comment '备注'",
            'more_json'  => "json comment '预留字段'",
            'depart_id'  => "int(18) comment '部门ID'",
            'alias_key'  => "varchar(100) comment '别名'",
            'create_user_id'    => "int(10) UNSIGNED comment '创建用户ID'",
            'delete_user_id'    => "int(10) UNSIGNED comment '删除用户ID'",
            'created_at'    => "timestamp DEFAULT CURRENT_TIMESTAMP comment '创建时间'",
            'updated_at'    => "timestamp DEFAULT CURRENT_TIMESTAMP comment '更新时间'",
            'deleted_at'    => "timestamp comment '删除时间'",
        ];

        $totalSqlArr = [];
        foreach ($tableArr as $idx => $table){

            $realTable = env("DB_PREFIX") .  $table;
            if($table == 'aa_template'){
                $realTable = $table;
                // continue;
            }

            $sql = "SELECT * FROM information_schema.COLUMNS WHERE TABLE_NAME = '{$realTable}'";
            $queryResult = DB::connection($conn)->select($sql);

            // 字段过滤
            $columnNameArr = [];
            $modifyColumnSqlArr = [];
            $typeSqlArr = [];
            foreach ($queryResult as $columnInfo){
                $columnName = $columnInfo->COLUMN_NAME;
                $columnNameArr[] = $columnInfo->COLUMN_NAME;

                // 更新字段类型
                if(in_array($columnName, array_keys($fieldLengthMap))){
                    // ALTER TABLE address MODIFY COLUMN city bigint(20);
                    $type = $fieldLengthMap[$columnName];
                    $sql = "ALTER TABLE `{$realTable}` MODIFY COLUMN `{$columnName}` {$type};";
                    $modifyColumnSqlArr[] = $sql;
                    $typeSqlArr[] = $sql;
                }
            }

            $modifyColumnSqlArrStr = implode(" ", $modifyColumnSqlArr);
         //   DB::connection($conn)->getPdo()->exec($modifyColumnSqlArrStr);
           // dd(123);


            // 当前表最后一列
            $afterColumn = end($columnNameArr);

            $finalFieldOrderArr = [];
            foreach ($fieldOrderArr as $field){
                if(in_array($field, $columnNameArr)){
                    $finalFieldOrderArr[] = $field;
                }
            }

            // ALTER TABLE `aa_template` MODIFY `remark` text AFTER `is_enable`
            // 获取最终的sql语句
            $sqlArr = [];
            foreach ($finalFieldOrderArr as $idx => $field){
                foreach ($queryResult as $columnInfo){
                    $columnName = $columnInfo->COLUMN_NAME;
                    if($columnName == $field){
                        $sql = "ALTER TABLE `{$realTable}` MODIFY `{$columnName}` {$columnInfo->COLUMN_TYPE} AFTER `{$afterColumn}`;";
                        $afterColumn = $columnName;
                        $sqlArr[] = $sql;
                        $totalSqlArr[] = $sql;
                        break;
                    }

                }
            }

            // sql 执行顺序
            foreach ($typeSqlArr as $typeSql){
                $totalSqlArr[] = $typeSql;
            }

            // 执行
            $sqlArrStr = implode(" ", $sqlArr);
            DB::connection($conn)->getPdo()->exec($sqlArrStr);
        }

        $totalSqlArrStr = implode(" ", $totalSqlArr);
      //  dd($totalSqlArrStr);
        DB::connection($conn)->getPdo()->exec($totalSqlArrStr);


        return $totalSqlArr;
    }

    // 批量更新
    public static function updateBatch($tableName, $multipleData = [])
    {
        try {
            if (empty($multipleData)) {
                throw new \Exception("数据不能为空");
            }

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

}
