<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\DTO;

use Fresns\DTO\DTO;

class CommonUploadFileDTO extends DTO
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['integer', 'required', 'in:1,2,3,4'],
            'tableType' => ['integer', 'required', 'between:1,11'],
            'tableName' => ['string', 'required'],
            'tableColumn' => ['string', 'required'],
            'tableId' => ['integer', 'nullable', 'required_without:tableKey'],
            'tableKey' => ['string', 'nullable', 'required_without:tableId'],
            'uploadMode' => ['integer', 'required', 'in:1,2'],
            'file' => ['file', 'nullable', 'required_if:uploadMode,1'],
            'moreJson' => ['string', 'nullable'],
            'fileInfo' => ['string', 'nullable', 'required_if:uploadMode,2'],
        ];
    }
}
