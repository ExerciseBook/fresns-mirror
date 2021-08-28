<?php
/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Center\Common;

use App\Helpers\DBHelper;
use App\Traits\ApiTrait;
use App\Traits\ServerTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class ValidateService
{
    use ApiTrait;

    public static $validator = null;

    public static function validateRule(Request $request, $rule, $message = [])
    {
        self::$validator = \Validator::make($request->all(), $rule, $message);
        if (self::$validator->fails()) {
            self::showError();
        }
    }

    public static function validateRuleDirect($inputArr, $rule, $message = [])
    {
        self::$validator = \Validator::make($inputArr, $rule, $message);
        if (self::$validator->fails()) {
            self::showError();
        }
    }

    public static function showError()
    {
        $data = self::$validator->errors();
        (new self)->error(ErrorCodeService::CODE_PARAM_ERROR, $data);
    }

    // 校验服务规则
    public static function validateServerRule($params, $rule)
    {
        self::$validator = \Validator::make($params, $rule);
        if (self::$validator->fails()) {
            $info = self::$validator->errors();

            return $info;
        }

        return true;
    }

    protected static $validMap = [
        'default.enable' => [
            'id' => 'required',
        ],
    ];

    // 检查id是否存在表中
    public static function existInTable($idArr, $table)
    {
        if (! is_array($idArr)) {
            return false;
        }

        if (count($idArr) == 0) {
            return false;
        }
        $conn = DBHelper::getConnectionName($table);

        $queryCount = DB::connection($conn)->table($table)->whereIn('id', $idArr)->count();

        return count($idArr) === $queryCount;
    }

    // 检查id是否存在表中
    public static function idsStrExistInTable($idStr, $table)
    {
        $idArr = explode(',', $idStr);

        if (count($idArr) == 0) {
            return false;
        }

        $queryCount = DB::table($table)->whereIn('id', $idArr)->count();

        return count($idArr) === $queryCount;
    }

    //  验证数组字段
    public static function validParamExist($params, $checkParamsArr)
    {
        foreach ($checkParamsArr as $v) {
            if (! isset($params[$v]) || $params[$v] == '') {
                LogService::error("参数校验失败 [$v] ", $params);
                LogService::error('校验字段为: ', $checkParamsArr);

                return false;
//                (new self)->error(ErrorCodeService::CODE_EXCEPTION, $data);
            }
        }

        return true;
    }
}
