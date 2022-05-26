<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\DTO;

use Fresns\DTO\DTO;

class AccountWalletLogsDTO extends DTO
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['integer', 'nullable', 'in:1,2,3,4,5,6'],
            'status' => ['boolean', 'nullable'],
            'pageSize' => ['integer', 'nullable', 'between:1,50'],
            'page' => ['integer', 'nullable'],
        ];
    }
}
