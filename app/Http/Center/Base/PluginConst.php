<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Center\Base;

use App\Helpers\FileHelper;
use Illuminate\Support\Facades\File;

/**
 * Class PluginConst
 * 插件常量和路径相关的信息.
 */
class PluginConst
{
    // 插件描述符文件名称
    const PLUGIN_JSON_FILE_NAME = 'plugin.json';

    const PLUGIN_IMAGE_NAME = 'fresns.png';

    const PLUGIN_SKIP_DIR_ARR = ['.', '..'];

    const PLUGIN_TYPE_ENGINE = 1;
    const PLUGIN_TYPE_EXTENSION = 2;
    const PLUGIN_TYPE_MOBILE = 3;
    const PLUGIN_TYPE_CONTROLLER_PANEL = 4;
    const PLUGIN_TYPE_THEME = 5;
}
