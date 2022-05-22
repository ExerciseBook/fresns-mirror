<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\DTO;

use Fresns\DTO\DTO;

class GlobalConfigsDTO extends DTO
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'keys' => ['nullable', 'string'],
            'tags' => ['nullable', 'string'],
            'pageSize' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }
}
