<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\DTO;

use Fresns\DTO\DTO;

class GlobalRolesDTO extends DTO
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['nullable', 'in:1,2,3'],
            'pageSize' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }
}
