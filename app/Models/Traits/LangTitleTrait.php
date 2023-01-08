<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models\Traits;

use App\Models\Language;

trait LangTitleTrait
{
    public function titles()
    {
        return $this->hasMany(Language::class, 'table_id', 'id')
            ->where('table_column', 'title')
            ->where('table_name', $this->getTable());
    }

    public function getLangTitle($langTag)
    {
        return $this->titles->where('lang_tag', $langTag)->first()?->lang_content ?: $this->title;
    }
}
