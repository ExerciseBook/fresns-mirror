<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsConfigs;

// 配置
use App\Base\Config\BaseConfig;

class AmConfig extends BaseConfig
{
    // 主表
    const CFG_TABLE = 'configs';

    // 主表额外搜索字段
    const ADDED_SEARCHABLE_FIELDS = [
        'ids' => ['field' => 'id', 'op' => 'IN'],
        'item_key' => ['field' => 'item_key', 'op' => '='],
        'item_key_no' => ['field' => 'item_key', 'op' => '<>'],
        'item_keys' => ['field' => 'item_key', 'op' => 'IN'],
        'item_tag' => ['field' => 'item_tag', 'op' => '='],
    ];

    //新增订阅
    const SUB_PLUGINS = 'subscribe_plugins';

    //站点设置
    const SITE_SETTING = 'site_setting';
    //短信设置
    const SMS_SETTING = 'sms_setting';
    //发信配置
    const SEND_SETTING = 'send_setting';
    //其他设置
    const OTHER_SETTING = 'other_setting';

    //七大洲编号
    const CONTINENTS = 'continents';
    //地图服务商
    const MAP = 'maps';
    //语言代码
    const LANGUAGE_CODES = 'language_codes';
    //地区编码
    const AREAS = 'areas_codes';

    //语言设置
    const LANG_SETTINGS = 'language_menus';
    //多语言相关配置
    const LANGUAGE = 'language_menus';
    //是否开启多语言
    const LANGUAGE_STATUS = 'language_status';
    //默认
    const DEFAULT_LANGUAGE = 'default_language';

    //用户修改间隔天数
    const MNAME_EDIT = 'mname_edit';

    //用户昵称修改间隔天数
    const NICKNAME_EDIT = 'nickname_edit';

    //存储配置
    const STORAGE = 'storageImages';

    const VIDEO_STORAGE = 'storageVideos';

    const AUDIO_STORAGE = 'storageAudios';

    const DOC_STORAGE = 'storageDocs';

    //控制台域名
    const BACKEND_DOMAIN = 'backend_domain';

    //登录入口
    const BACKEND_PATH = 'backend_path';

    //站点域名
    const SITE_DOMAIN = 'site_domain';

    const LENGTHUNITS_OPTION = [
        ['key' => 'km', 'text' => '公里 km'],
        ['key' => 'mi', 'text' => '英里 mi'],
    ];
    //日期格式
    const DATE_OPTION = [
        ['key' => 1, 'text' => 'yyyy-mm-dd'],
        ['key' => 2, 'text' => 'yyyy/mm/dd'],
        ['key' => 3, 'text' => 'yyyy.mm.dd'],
        ['key' => 4, 'text' => 'mm-dd-yyyy'],
        ['key' => 5, 'text' => 'mm/dd/yyyy'],
        ['key' => 6, 'text' => 'mm.dd.yyyy'],
        ['key' => 7, 'text' => 'dd-mm-yyyy'],
        ['key' => 8, 'text' => 'dd/mm/yyyy'],
        ['key' => 9, 'text' => 'dd.mm.yyyy'],
    ];

    const SITE_PRIVATE_END_OPTION = [
        ['key' => 1, 'text' => '站点内容不可见'],
        ['key' => 2, 'text' => '到期前内容可见，新内容不可见'],
    ];

    // model 使用 - 表单映射
    const FORM_FIELDS_MAP = [
        'id' => 'id',
        'rank_num' => 'rank_num',
        'is_enable' => 'is_enable',
        'item_key' => 'item_key',
        'item_value' => 'item_value',
        'item_tag' => 'item_tag',
        'item_type' => 'item_type',
    ];
}
