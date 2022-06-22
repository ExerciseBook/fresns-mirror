<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait FsidTrait
{
    public static function bootFsidTrait()
    {
        static::creating(function ($model) {
            $model->{$this->getFsidKey()} = $model->{$this->getFsidKey()} ?? static::generateFsid();
        });
    }

    // generate fsid
    public static function generateFsid(): string
    {
        $fsid = Str::random(12);

        $checkFsid = static::fsid($fsid)->first();

        if (! $checkFsid) {
            return $fsid;
        }

        return static::generateFsid();
    }

    public function scopeFsid($query, string $fsid)
    {
        return $query->where($this->getFsidKey(), $fsid);
    }
}
