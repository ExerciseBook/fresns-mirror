<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use App\Models\Account;
use App\Models\Comment;
use App\Models\Config;
use App\Models\Extend;
use App\Models\File;
use App\Models\Group;
use App\Models\Hashtag;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class PrimaryHelper
{
    // get model
    public static function fresnsModelByFsid(string $modelName, ?string $fsid = null)
    {
        if (empty($fsid)) {
            return null;
        }

        $cacheKey = "fresns_model_{$modelName}_{$fsid}";
        $cacheTime = CacheHelper::fresnsCacheTimeByFileType();

        $fresnsModel = Cache::remember($cacheKey, $cacheTime, function () use ($modelName, $fsid) {
            switch ($modelName) {
                // account
                case 'account':
                    $model = Account::withTrashed()->where('aid', $fsid)->first();
                break;

                // user
                case 'user':
                    $model = User::withTrashed()->where('uid', $fsid)->orWhere('username', $fsid)->first();
                break;

                // group
                case 'group':
                    $model = Group::withTrashed()->where('gid', $fsid)->first();
                break;

                // hashtag
                case 'hashtag':
                    $model = Hashtag::withTrashed()->where('slug', $fsid)->first();
                break;

                // post
                case 'post':
                    $model = Post::withTrashed()->with('postAppend')->where('pid', $fsid)->first();
                break;

                // comment
                case 'comment':
                    $model = Comment::withTrashed()->with('commentAppend')->where('cid', $fsid)->first();
                break;

                // file
                case 'file':
                    $model = File::withTrashed()->with('fileAppend')->where('fid', $fsid)->first();
                break;

                // extend
                case 'extend':
                    $model = Extend::withTrashed()->where('eid', $fsid)->first();
                break;
            }

            return $model;
        });

        if (empty($fresnsModel)) {
            Cache::forget($cacheKey);
        }

        return $fresnsModel;
    }

    // get table id
    public static function fresnsPrimaryId(string $tableName, ?string $tableKey = null)
    {
        if (empty($tableKey)) {
            return null;
        }

        $tableId = match ($tableName) {
            'config' => PrimaryHelper::fresnsConfigIdByItemKey($tableKey),
            'account' => PrimaryHelper::fresnsAccountIdByAid($tableKey),
            'user' => PrimaryHelper::fresnsUserIdByUidOrUsername($tableKey),
            'group' => PrimaryHelper::fresnsGroupIdByGid($tableKey),
            'hashtag' => PrimaryHelper::fresnsHashtagIdByHid($tableKey),
            'post' => PrimaryHelper::fresnsPostIdByPid($tableKey),
            'comment' => PrimaryHelper::fresnsCommentIdByCid($tableKey),
            'file' => PrimaryHelper::fresnsFileIdByFid($tableKey),
            'extend' => PrimaryHelper::fresnsExtendIdByEid($tableKey),

            'configs' => PrimaryHelper::fresnsConfigIdByItemKey($tableKey),
            'accounts' => PrimaryHelper::fresnsAccountIdByAid($tableKey),
            'users' => PrimaryHelper::fresnsUserIdByUidOrUsername($tableKey),
            'groups' => PrimaryHelper::fresnsGroupIdByGid($tableKey),
            'hashtags' => PrimaryHelper::fresnsHashtagIdByHid($tableKey),
            'posts' => PrimaryHelper::fresnsPostIdByPid($tableKey),
            'comments' => PrimaryHelper::fresnsCommentIdByCid($tableKey),
            'files' => PrimaryHelper::fresnsFileIdByFid($tableKey),
            'extends' => PrimaryHelper::fresnsExtendIdByEid($tableKey),

            default => null,
        };

        return $tableId;
    }

    /**
     * @param  string  $itemKey
     * @return int |null
     */
    public static function fresnsConfigIdByItemKey(?string $itemKey = null)
    {
        if (empty($itemKey)) {
            return null;
        }

        $id = Config::withTrashed()->where('item_key', $itemKey)->value('id');

        return $id ?? null;
    }

    /**
     * @param  string  $aid
     * @return int |null
     */
    public static function fresnsAccountIdByAid(?string $aid = null)
    {
        if (empty($aid)) {
            return null;
        }

        return PrimaryHelper::fresnsModelByFsid('account', $aid)?->id;
    }

    /**
     * @param  string  $uid
     * @return int |null
     */
    public static function fresnsAccountIdByUidOrUsername(?string $uidOrUsername = null)
    {
        if (empty($uidOrUsername)) {
            return null;
        }

        return PrimaryHelper::fresnsModelByFsid('user', $uidOrUsername)?->account_id;
    }

    /**
     * @param  string  $uidOrUsername
     * @return int |null
     */
    public static function fresnsUserIdByUidOrUsername(?string $uidOrUsername = null)
    {
        if (empty($uidOrUsername)) {
            return null;
        }

        return PrimaryHelper::fresnsModelByFsid('user', $uidOrUsername)?->id;
    }

    /**
     * @param  string  $gid
     * @return int |null
     */
    public static function fresnsGroupIdByGid(?string $gid = null)
    {
        if (empty($gid)) {
            return null;
        }

        return PrimaryHelper::fresnsModelByFsid('group', $gid)?->id;
    }

    /**
     * @param  string  $hid
     * @return int |null
     */
    public static function fresnsHashtagIdByHid(?string $hid = null)
    {
        if (empty($hid)) {
            return null;
        }

        return PrimaryHelper::fresnsModelByFsid('hashtag', $hid)?->id;
    }

    /**
     * @param  string  $pid
     * @return int |null
     */
    public static function fresnsPostIdByPid(?string $pid = null)
    {
        if (empty($pid)) {
            return null;
        }

        return PrimaryHelper::fresnsModelByFsid('post', $pid)?->id;
    }

    /**
     * @param  string  $cid
     * @return int |null
     */
    public static function fresnsCommentIdByCid(?string $cid = null)
    {
        if (empty($cid)) {
            return null;
        }

        return PrimaryHelper::fresnsModelByFsid('comment', $cid)?->id;
    }

    /**
     * @param  string  $fid
     * @return int |null
     */
    public static function fresnsFileIdByFid(?string $fid = null)
    {
        if (empty($fid)) {
            return null;
        }

        return PrimaryHelper::fresnsModelByFsid('file', $fid)?->id;
    }

    /**
     * @param  string  $eid
     * @return int |null
     */
    public static function fresnsExtendIdByEid(?string $eid = null)
    {
        if (empty($eid)) {
            return null;
        }

        return PrimaryHelper::fresnsModelByFsid('extend', $eid)?->id;
    }
}
