<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models\Traits;

use App\Helpers\ConfigHelper;
use App\Helpers\DateHelper;
use App\Helpers\PluginHelper;
use App\Helpers\StrHelper;
use App\Models\AccountConnect;
use App\Models\AccountWallet;

trait CommentServiceTrait
{
    public function getCommentInfo(string $langTag = '', string $timezone = '')
    {
        $commentData = $this;

        $info['cid'] = $commentData->cid;

        return $info;
    }
}
