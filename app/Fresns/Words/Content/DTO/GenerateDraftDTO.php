<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Words\Content\DTO;

use Fresns\DTO\DTO;

class GenerateDraftDTO extends DTO
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'uid' => ['integer', 'required', 'exists:App\Models\User,uid'],
            'type' => ['integer', 'required', 'in:1,2'],
            'hname' => ['string', 'nullable'],
            'anonymous' => ['boolean', 'nullable'],
            'gid' => ['string', 'nullable'],
            'pid' => ['string', 'nullable', 'required_if:type,2'],
            'fsid' => ['string', 'nullable'],
            'sessionLog' => ['json', 'required'],
        ];
    }
}
