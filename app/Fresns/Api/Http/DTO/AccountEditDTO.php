<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\DTO;

use Fresns\DTO\DTO;

class AccountEditDTO extends DTO
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'codeType' => ['string', 'nullable', 'in:email,phone'],
            'verifyCode' => ['string', 'nullable'],
            'newVerifyCode' => ['string', 'nullable'],
            'editEmail' => ['string', 'nullable'],
            'editPhone' => ['integer', 'nullable'],
            'editCountryCode' => ['integer', 'nullable', 'required_with:editPhone'],
            'password' => ['string', 'nullable'],
            'editPassword' => ['string', 'nullable'],
            'walletPassword' => ['string', 'nullable'],
            'editWalletPassword' => ['string', 'nullable'],
            'deleteConnect' => ['integer', 'nullable', 'between:1,17'],
            'editLastLoginTime' => ['string', 'nullable', 'date_format:"Y-m-d H:i:s"'],
        ];
    }
}
