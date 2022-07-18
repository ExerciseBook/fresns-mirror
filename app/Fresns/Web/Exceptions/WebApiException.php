<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Web\Exceptions;

use App\Fresns\Api\Traits\ApiResponseTrait;

class WebApiException extends \Exception
{
    use ApiResponseTrait;

    public function render($request)
    {
        // hashtag 不存在
        // if (in_array($this->getCode(), [37200])) {
            return view('error', [
                'code' => $this->getCode(),
                'message' => $this->getMessage(),
            ]);
        // }

        return back()->with([
            'code' => $this->getCode(),
            'failure' => $this->getMessage(),
        ]);
    }
}
