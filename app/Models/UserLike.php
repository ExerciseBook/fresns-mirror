<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models;

class UserLike extends Model
{
    const MARK_TYPE_LIKE = 1;
    const MARK_TYPE_DISLIKE = 2;

    const TYPE_USER = 1;
    const TYPE_GROUP = 2;
    const TYPE_HASHTAG = 3;
    const TYPE_POST = 4;
    const TYPE_COMMENT = 5;

    public function scopeMarkType($query, int $type)
    {
        return $query->where('mark_type', $type);
    }

    public function scopeType($query, int $type)
    {
        return $query->where('like_type', $type);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function likeUser()
    {
        return $this->belongsTo(User::class, 'like_id', 'id')->where('mark_type', UserLike::MARK_TYPE_LIKE)->where('like_type', UserLike::TYPE_USER);
    }

    public function likeGroup()
    {
        return $this->belongsTo(Group::class, 'like_id', 'id')->where('mark_type', UserLike::MARK_TYPE_LIKE)->where('like_type', UserLike::TYPE_GROUP);
    }

    public function likeHashtag()
    {
        return $this->belongsTo(Hashtag::class, 'like_id', 'id')->where('mark_type', UserLike::MARK_TYPE_LIKE)->where('like_type', UserLike::TYPE_HASHTAG);
    }

    public function likePost()
    {
        return $this->belongsTo(Post::class, 'like_id', 'id')->where('mark_type', UserLike::MARK_TYPE_LIKE)->where('like_type', UserLike::TYPE_POST);
    }

    public function likeComment()
    {
        return $this->belongsTo(Comment::class, 'like_id', 'id')->where('mark_type', UserLike::MARK_TYPE_LIKE)->where('like_type', UserLike::TYPE_COMMENT);
    }

    public function dislikeUser()
    {
        return $this->belongsTo(User::class, 'like_id', 'id')->where('mark_type', UserLike::MARK_TYPE_DISLIKE)->where('like_type', UserLike::TYPE_USER);
    }

    public function dislikeGroup()
    {
        return $this->belongsTo(Group::class, 'like_id', 'id')->where('mark_type', UserLike::MARK_TYPE_DISLIKE)->where('like_type', UserLike::TYPE_GROUP);
    }

    public function dislikeHashtag()
    {
        return $this->belongsTo(Hashtag::class, 'like_id', 'id')->where('mark_type', UserLike::MARK_TYPE_DISLIKE)->where('like_type', UserLike::TYPE_HASHTAG);
    }

    public function dislikePost()
    {
        return $this->belongsTo(Post::class, 'like_id', 'id')->where('mark_type', UserLike::MARK_TYPE_DISLIKE)->where('like_type', UserLike::TYPE_POST);
    }

    public function dislikeComment()
    {
        return $this->belongsTo(Comment::class, 'like_id', 'id')->where('mark_type', UserLike::MARK_TYPE_DISLIKE)->where('like_type', UserLike::TYPE_COMMENT);
    }
}
