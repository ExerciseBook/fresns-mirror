<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models;

class Archive extends Model
{
    const TYPE_USER = 1;
    const TYPE_GROUP = 2;
    const TYPE_HASHTAG = 3;
    const TYPE_POST = 4;
    const TYPE_COMMENT = 5;

    const FORM_TYPE_INPUT = 1;
    const FORM_TYPE_TEXTAREA = 2;
    const FORM_TYPE_SELECT = 3;
    const FORM_TYPE_SELECT_MULTIPLE = 4;
    const FORM_TYPE_FILE_IMAGE = 5;
    const FORM_TYPE_FILE_VIDEO = 6;
    const FORM_TYPE_FILE_AUDIO = 7;
    const FORM_TYPE_FILE_DOCUMENT = 8;
    const FORM_TYPE_INTEGER = 9;
    const FORM_TYPE_NUMERIC = 10;
    const FORM_TYPE_RANGE = 11;
    const FORM_TYPE_DATE = 12;
    const FORM_TYPE_DATETIME = 13;
    const FORM_TYPE_EMAIL = 14;
    const FORM_TYPE_PHONE = 15;
    const FORM_TYPE_URL = 16;
    const FORM_TYPE_COLOR = 17;

    use Traits\IsEnableTrait;

    protected $casts = [
        'options' => 'array',
    ];

    public function scopeType($query, int $type)
    {
        return $query->where('use_type', $type);
    }
}
