<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Center\Base;

use App\Base\Controllers\BaseController;
use App\Traits\BladePluginTrait;

class BasePluginWebController extends BaseController
{
    use BladePluginTrait;

    public $pluginConfig;

    public $themePc;
    public $themeMobile;
}
