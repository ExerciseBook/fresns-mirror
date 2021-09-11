<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Info;

use App\Base\Resources\BaseAdminResource;

/**
 * List resource config handle
 */

class FresnsInfoStopWordsResource extends BaseAdminResource
{
    public function toArray($request)
    {

        // Default Field
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
