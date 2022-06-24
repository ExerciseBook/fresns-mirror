<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\DTO;

use Fresns\DTO\DTO;

class EditorCreateDTO extends DTO
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['string', 'required', 'in:post,comment'],
            'source' => ['integer', 'required', 'in:1,2'],
            'editorUnikey' => ['string', 'nullable'],
            'fsid' => ['string', 'nullable'],
            'pid' => ['string', 'nullable', 'required_if:type,comment'],
            'gid' => ['string', 'nullable'],
            'hname' => ['string', 'nullable'],
            'isAnonymous' => ['boolean', 'nullable'],
        ];
    }
}
