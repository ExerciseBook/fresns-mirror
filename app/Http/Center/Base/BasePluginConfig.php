<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Center\Base;

class BasePluginConfig
{
    /**
     * 插件名称
     *
     * @var string
     */
    public $name = '';

    /**
     * 插件类型
     *
     * @var string
     */
    public $type = '';

    /**
     * 插件描述
     *
     * @var string
     */
    public $description = '';

    /**
     * 插件图片 url
     *
     * @var string
     */
    public $imageUrl = '';

    /**
     * 插件作者
     *
     * @var string
     */
    public $author = '作者';

    /**
     * 插件作者链接
     *
     * @var string
     */
    public $authorLink = '';
    /**
     * 插件使用场景
     * 如 短信服务商/文件存储服务商 等
     *
     * @var string
     */
    public $sceneArr = [

    ];

    /**
     * 插件全局唯一值
     *
     * @var string
     */
    public $uniKey = '';

    /**
     * 插件最新三位版本号
     *
     * @var string
     */
    public $currVersion = '1.0.0';

    /**
     * 插件最新整型版本号
     *
     * @var int
     */
    public $currVersionInt = 1;

    /**
     * 插件目录名称, 小驼峰, 注意大小写
     * app/Plugins/Addons/$dirName
     *
     * @var string
     */
    public $dirName = '';

    /**
     * PC端模版名称
     *
     * @var string
     */
    public $themePc = '';

    /**
     * H5端模版名称
     *
     * @var string
     */
    public $themeMobile = '';

    /**
     * 插件访问路径
     * 相对路径，支持变量名, 同“站点网址”拼接成完整 URL
     *
     * @var string
     */
    public $accessPath = '';

    /**
     * 插件设置路径
     *
     * @var string
     */
    public $settingPath = '';

    /**
     * 插件网址
     *
     * @var string
     */
    public $pluginUrl = '';

    // 插件默认命令字, 任何插件必须要要有
    public const PLG_CMD_DEFAULT = 'plg_cmd_default';

    // 插件命令字回调映射
    const PLG_CMD_HANDLE_MAP = [
        self::PLG_CMD_DEFAULT => 'defaultHandler',
    ];

    // 插件错误码
    const OK = 0;
    const FAIL = 1001;
    const CODE_NOT_EXIST = 1002;
    const CODE_PARAMS_ERROR = 1003;

    // 插件错误码映射
    const CODE_MAP = [
        self::OK => 'ok',
        self::FAIL => 'fail',
        self::CODE_NOT_EXIST => '数据不存在',
        self::CODE_PARAMS_ERROR => '参数错误',
    ];

    public function getThemePc()
    {
        return $this->uniKey."Pc";
    }

    public function getThemeMobile()
    {
        return $this->uniKey."Mobile";
    }

    public function getViewSetting()
    {
        return $this->uniKey."Setting";
    }


}
