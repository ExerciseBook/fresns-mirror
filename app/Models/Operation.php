<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models;

class Operation extends Model
{
    const TYPE_CUSTOM = 1;
    const TYPE_BUTTON_ICON = 2;
    const TYPE_DIVERSIFY_IMAGE = 3;
    const TYPE_TIP_TEXT = 4;

    const USE_TYPE_GENERAL = 1;
    const USE_TYPE_FUNCTION = 2;

    use Traits\IsEnableTrait;
}
