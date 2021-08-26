<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsStopWords;

use App\Base\Randoms\BaseRandom;
use Faker\Generator;

class AmRandom extends BaseRandom
{
    protected $table = AmConfig::CFG_TABLE;

    // 生成数据前置表, 请填写
    protected $genBeforeTables = [];

    // 清空数据前置表, 请填写
    protected $clearBeforeTables = [];

    // 当前表附加的普通字段
    protected $addedRandomMap = [
        // 'school_name'   => RandomHelper::RAND_STRING,
    ];

    // 当前表附加的特殊字段
    protected function genSpecialRandData(Generator $faker)
    {
        $specItem = [];

        return $specItem;
    }
}
