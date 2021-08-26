<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Base\Exceptions;

class NotFoundException extends BaseException
{
    public function __construct(string $path)
    {
        parent::__construct(sprintf('The "%s" does not exist', $path));
    }
}
