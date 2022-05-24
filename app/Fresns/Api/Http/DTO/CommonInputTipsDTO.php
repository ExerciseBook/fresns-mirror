<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\DTO;

use Fresns\DTO\DTO;

class CommonInputTipsDTO extends DTO
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'integer', 'in:1,2,3,4,5,6'],
            'key' => ['required', 'string'],
        ];
    }
}
