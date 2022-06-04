<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models;

class UserBlock extends Model
{
    const TYPE_USER = 1;
    const TYPE_GROUP = 2;
    const TYPE_HASHTAG = 3;
    const TYPE_POST = 4;
    const TYPE_COMMENT = 5;

    public function scopeType($query, int $type)
    {
        return $query->where('block_type', $type);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function blockUser()
    {
        return $this->belongsTo(User::class, 'block_id', 'id')->where('block_type', UserBlock::TYPE_USER);
    }

    public function blockGroup()
    {
        return $this->belongsTo(Group::class, 'block_id', 'id')->where('block_type', UserBlock::TYPE_GROUP);
    }

    public function blockHashtag()
    {
        return $this->belongsTo(Hashtag::class, 'block_id', 'id')->where('block_type', UserBlock::TYPE_HASHTAG);
    }

    public function blockPost()
    {
        return $this->belongsTo(Post::class, 'block_id', 'id')->where('block_type', UserBlock::TYPE_POST);
    }

    public function blockComment()
    {
        return $this->belongsTo(Comment::class, 'block_id', 'id')->where('block_type', UserBlock::TYPE_COMMENT);
    }
}
