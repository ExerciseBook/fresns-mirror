<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Words\Account\DTO;

use Fresns\DTO\DTO;

class WalletDecreaseDTO extends DTO
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'aid' => ['string', 'required'],
            'uid' => ['integer', 'nullable'],
            'password' => ['string', 'nullable'],
            'amountTotal' => ['numeric', 'required'],
            'systemFee' => ['numeric', 'required'],
            'originAid' => ['string', 'nullable'],
            'originUid' => ['integer', 'nullable'],
            'transactionUnikey' => ['string', 'required', 'exists:App\Models\Plugin,unikey'],
            'transactionId' => ['integer', 'nullable'],
            'transactionCode' => ['string', 'nullable'],
            'remark' => ['string', 'nullable'],
            'moreJson' => ['json', 'nullable'],
        ];
    }
}
