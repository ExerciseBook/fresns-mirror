<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Info;

use App\Base\Resources\BaseAdminResource;

class FresnsInfoStopWordsResource extends BaseAdminResource
{
    public function toArray($request)
    {

        // 默认字段
        $default = [
            'word' => $this->word,
            'contentMode' => $this->content_mode,
            'userMode' => $this->member_mode,
            'dialogMode' => $this->dialog_mode,
            'replaceWord' => $this->replace_word,
        ];

        return $default;
    }
}
