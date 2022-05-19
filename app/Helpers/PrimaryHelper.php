<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use App\Models\Account;
use App\Models\Comment;
use App\Models\Extend;
use App\Models\File;
use App\Models\Group;
use App\Models\Hashtag;
use App\Models\Post;
use App\Models\User;

class PrimaryHelper
{
    /**
     * @param  string  $aid
     * @return int |null
     */
    public static function fresnsAccountIdByAid(?string $aid = null)
    {
        if (empty($aid)) {
            return null;
        }

        $id = Account::withTrashed()->where('aid', $aid)->value('id');

        return $id ?? null;
    }

    /**
     * @param  string  $aid
     * @return int |null
     */
    public static function fresnsAccountIdByUid(?string $uid = null)
    {
        if (empty($uid)) {
            return null;
        }

        $id = User::withTrashed()->where('uid', $uid)->value('account_id');

        return $id ?? null;
    }

    /**
     * @param  string  $aid
     * @return int |null
     */
    public static function fresnsUserIdByUid(?string $aid = null)
    {
        if (empty($aid)) {
            return null;
        }

        $id = User::withTrashed()->where('uid', $aid)->value('id');

        return $id ?? null;
    }

    /**
     * @param  string  $username
     * @return int |null
     */
    public static function fresnsUserIdByUsername(string $username)
    {
        $id = User::withTrashed()->where('username', $username)->value('id');

        return $id ?? null;
    }

    /**
     * @param  string  $gid
     * @return int |null
     */
    public static function fresnsGroupIdByGid(string $gid)
    {
        $id = Group::withTrashed()->where('gid', $gid)->value('id');

        return $id ?? null;
    }

    /**
     * @param  string  $huri
     * @return int |null
     */
    public static function fresnsHashtagIdByHid(string $hid)
    {
        $id = Hashtag::withTrashed()->where('slug', $hid)->value('id');

        return $id ?? null;
    }

    /**
     * @param  string  $pid
     * @return int |null
     */
    public static function fresnsPostIdByPid(string $pid)
    {
        $id = Post::withTrashed()->where('pid', $pid)->value('id');

        return $id ?? null;
    }

    /**
     * @param  string  $cid
     * @return int |null
     */
    public static function fresnsCommentIdByCid(string $cid)
    {
        $id = Comment::withTrashed()->where('cid', $cid)->value('id');

        return $id ?? null;
    }

    /**
     * @param  string  $fid
     * @return int |null
     */
    public static function fresnsFileIdByFid(string $fid)
    {
        $id = File::withTrashed()->where('fid', $fid)->value('id');

        return $id ?? null;
    }

    /**
     * @param  string  $eid
     * @return int |null
     */
    public static function fresnsExtendIdByEid(string $eid)
    {
        $id = Extend::withTrashed()->where('eid', $eid)->value('id');

        return $id ?? null;
    }
}
