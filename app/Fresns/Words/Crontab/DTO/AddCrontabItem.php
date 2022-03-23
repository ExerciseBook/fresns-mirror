<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Words\Crontab\DTO;

use Fresns\DTO\DTO;

/**
 * Class AddCrontabItem.
 */
class AddCrontabItem extends DTO
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'unikey'=>'string',
            'cmdWord'=>'string',
            'taskPeriod' => 'string',
        ];
    }
}
