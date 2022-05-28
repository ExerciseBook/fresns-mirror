<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Words\File\DTO;

use Fresns\DTO\DTO;

class GetAntiLinkFileInfoDTO extends DTO
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['integer', 'required', 'in:1,2,3,4'],
            'fileId' => ['integer', 'nullable', 'required_without:fid', 'exists:App\Models\File,id'],
            'fid' => ['string', 'nullable', 'required_without:fileId', 'exists:App\Models\File,fid'],
        ];
    }
}
