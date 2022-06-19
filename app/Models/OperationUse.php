<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models;

class OperationUse extends Model
{
    const TYPE_USER = 1;
    const TYPE_GROUP = 2;
    const TYPE_HASHTAG = 3;
    const TYPE_POST = 4;
    const TYPE_COMMENT = 5;

    public function scopeType($query, int $type)
    {
        return $query->where('use_type', $type);
    }

    public function operation()
    {
        return $this->belongsTo(Operation::class, 'operation_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'use_id', 'id')->where('use_type', OperationUse::TYPE_USER);
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'use_id', 'id')->where('use_type', OperationUse::TYPE_GROUP);
    }

    public function hashtag()
    {
        return $this->belongsTo(Hashtag::class, 'use_id', 'id')->where('use_type', OperationUse::TYPE_HASHTAG);
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'use_id', 'id')->where('use_type', OperationUse::TYPE_POST);
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class, 'use_id', 'id')->where('use_type', OperationUse::TYPE_COMMENT);
    }
}
