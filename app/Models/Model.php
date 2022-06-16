<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 编码风格
 * 
 class XxxModel
{
    // const 常量定义区域
    // use Trait 定义区域
    // static 属性定义区域
    // 属性定义区域
    // static 父级 Model 函数定义区域
    // scope 函数定义区域
    // setXxxYyyAttribute 定义区域
    // getXxxYyyAttribute 定义区域
    // xxxYyy(): Attribute 定义区域
    // relations 关系定义区域
    // 自定义函数定义区域
    // 静态自定义函数定义区域
}
 */
class Model extends BaseModel
{
    use HasFactory;
    use SoftDeletes;
    use Traits\DataChangeNotifyTrait;

    protected $guarded = [];

    protected $dates = [
        'deleted_at',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }
}
