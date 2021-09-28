<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Info;

use App\Base\Resources\BaseAdminResource;
use App\Http\FresnsDb\FresnsPluginCallbacks\FresnsPluginCallbacksConfig;
/**
 * List resource config handle
 */

class FresnsPluginCallbackResource extends BaseAdminResource
{
    public function toArray($request)
    {
        $content = $this->content;
        return $content;
    }
}
