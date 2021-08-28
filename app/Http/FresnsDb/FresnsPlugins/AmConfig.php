<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsPlugins;

// 配置
use App\Base\Config\BaseConfig;

class AmConfig extends BaseConfig
{
    // 主表
    const CFG_TABLE = 'plugins';

    // 主表额外搜索字段
    const ADDED_SEARCHABLE_FIELDS = [

    ];
    const PLUGINS_TYPE = 2;
    const ENABLE_FALSE = 0;
    // 下载状态
    const NO_DOWNLOAD = 0;
    const DOWNLOAD = 1;
    // 是否新版本
    const NO_NEWVISION = 0;
    const NEWVISION = 1;
    //加入通道支持插件
    const PLUGINS_MODE = 'mode';
    //站内信服务商
    const PLUGINS_SITE = 'site';
    //邮件服务商
    const PLUGINS_EMAIL = 'email';
    //短信服务商
    const PLUGINS_SMS = 'sms';
    //手机推送服务商
    const PLUGINS_PHONE = 'phone';
    //微信推送服务商
    const PLUGINS_WECHAT = 'wechat';
    //图片存储服务商
    const PLUGINS_IMAGE = 'image';
    //视频存储服务商
    const PLUGINS_VIDEO = 'video';
    //音频存储服务商
    const PLUGINS_AUDIO = 'audio';
    //文档存储服务商
    const PLUGINS_FILE = 'file';
    //站点账号注册功能
    const PLUGINS_REGISTER = 'register';
    //验证码服务商
    const PLUGINS_VERIFICATION = 'verification';
    //地图服务商
    const PLUGINS_MAP = 'map';
    //多用户角色插件
    const MANY_USERS = 'many_users';

    // model 使用 - 表单映射
    const FORM_FIELDS_MAP = [
        'id' => 'id',
        'is_enable' => 'is_enable',
        'unikey' => 'unikey',
        'name' => 'name',
        'type' => 'type',
        'image' => 'image',
        'description' => 'description',
        'version' => 'version',
        'version_int' => 'version_int',
        'author' => 'author',
        'author_link' => 'author_link',
        'scene' => 'scene',
        'plugin_domain' => 'plugin_domain',
        'access_path' => 'access_path',
        'setting_path' => 'setting_path',
        'more_json' => 'more_json',
        'is_upgrade' => 'is_upgrade',
        'upgrade_version' => 'upgrade_version',
        'upgrade_version_int' => 'upgrade_version_int',
    ];
}
