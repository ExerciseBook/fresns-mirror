<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsImplants;

// 配置
use App\Base\Config\BaseConfig;

 

class AmConfig extends BaseConfig
{
    // 主表
    const CFG_TABLE = 'implants';

    // 主表额外搜索字段
    const ADDED_SEARCHABLE_FIELDS = [

    ];

    // model 使用 - 表单映射
    const FORM_FIELDS_MAP = [
        'id' => 'id',
        'implant_type' => 'implant_type',
        'implant_id' => 'implant_id',
        'implant_template' => 'implant_template',
        'implant_name' => 'implant_name',
        'plugin_unikey' => 'plugin_unikey',
        'type' => 'type',
        'target' => 'target',
        'value' => 'value',
        'support' => 'support',
        'position' => 'position',
        'starting_at' => 'starting_at',
        'expired_at' => 'expired_at',
    ];

     

}