<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsCodeMessages;

use App\Base\Services\BaseAdminService;

class FsService extends BaseAdminService
{
    public function __construct()
    {
        $this->model = new FsModel();
        $this->resource = FsResource::class;
        $this->resourceDetail = FsResourceDetail::class;
    }

    public function common()
    {
        $common = parent::common();

        return $common;
    }

    // Get the corresponding code message
    public static function getCodeMessage($pluginUnikey, $langTag, $code)
    {
        $message = FsModel::where('plugin_unikey', $pluginUnikey)->where('lang_tag', $langTag)->where('code', $code)->value('message');

        return $message;
    }
}
