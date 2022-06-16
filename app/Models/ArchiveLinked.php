<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models;

class ArchiveLinked extends Model
{
    const TYPE_USER = 1;
    const TYPE_GROUP = 2;
    const TYPE_HASHTAG = 3;
    const TYPE_POST = 4;
    const TYPE_COMMENT = 5;

    use Traits\IsEnableTrait;

    public function getArchiveValueAttribute($value)
    {
        $value = match ($this->archive->json_type) {
            default => throw new \Exception("unknown archive type {$this->archive->json_type}"),
            'array', 'object', 'plugins' => json_decode($value, true) ?? [],
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'number' => intval($value),
        };

        return $value;
    }

    public function setArchiveValueAttribute($value)
    {
        if (in_array($this->archive->json_type, ['array', 'plugins', 'object']) || is_array($value)) {
            $value = json_encode($value);
        }

        if ($this->item_type == 'boolean') {
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
        }

        if ($this->item_type == 'number') {
            $value = intval($value);
        }

        $this->attributes['archive_value'] = $value;
    }

    public function scopeType($query, int $type)
    {
        return $query->where('linked_type', $type);
    }

    public function archive()
    {
        return $this->belongsTo(Archive::class, 'archive_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'linked_id', 'id')->where('linked_type', OperationLinked::TYPE_USER);
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'linked_id', 'id')->where('linked_type', OperationLinked::TYPE_GROUP);
    }

    public function hashtag()
    {
        return $this->belongsTo(Hashtag::class, 'linked_id', 'id')->where('linked_type', OperationLinked::TYPE_HASHTAG);
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'linked_id', 'id')->where('linked_type', OperationLinked::TYPE_POST);
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class, 'linked_id', 'id')->where('linked_type', OperationLinked::TYPE_COMMENT);
    }
}
