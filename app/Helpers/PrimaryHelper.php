<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use App\Models\Account;
use App\Models\Archive;
use App\Models\Comment;
use App\Models\Config;
use App\Models\Conversation;
use App\Models\Extend;
use App\Models\File;
use App\Models\Group;
use App\Models\Hashtag;
use App\Models\Operation;
use App\Models\Post;
use App\Models\SessionKey;
use App\Models\User;
use App\Models\UserFollow;

class PrimaryHelper
{
    // get model by fsid
    public static function fresnsModelByFsid(string $modelName, ?string $fsid = null)
    {
        if (empty($fsid)) {
            return null;
        }

        $cacheKey = "fresns_model_{$modelName}_{$fsid}";
        if ($modelName == 'user') {
            $cacheKey = $cacheKey.'_by_fsid';
        }

        $cacheTags = match ($modelName) {
            'key' => 'fresnsSystems',
            'account' => ['fresnsAccounts', 'fresnsAccountModels'],
            'user' => ['fresnsUsers', 'fresnsUserModels'],
            'group' => ['fresnsGroups', 'fresnsGroupModels'],
            'hashtag' => ['fresnsHashtags', 'fresnsHashtagModels'],
            'post' => ['fresnsPosts', 'fresnsPostModels'],
            'comment' => ['fresnsComments', 'fresnsCommentModels'],
            'file' => ['fresnsModels', 'fresnsFiles'],
            'extend' => ['fresnsModels', 'fresnsExtends'],
            'archive' => ['fresnsModels', 'fresnsArchives'],
            default => 'fresnsModels',
        };

        // is known to be empty
        $isKnownEmpty = CacheHelper::isKnownEmpty($cacheKey);
        if ($isKnownEmpty) {
            return null;
        }

        $fresnsModel = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($fresnsModel)) {
            switch ($modelName) {
                // key
                case 'key':
                    $fresnsModel = SessionKey::where('app_id', $fsid)->first();
                break;

                // account
                case 'account':
                    $fresnsModel = Account::withTrashed()->with(['users', 'connects'])->where('aid', $fsid)->first();
                break;

                // user
                case 'user':
                    if (StrHelper::isPureInt($fsid)) {
                        $fresnsModel = User::withTrashed()->where('uid', $fsid)->first();
                    } else {
                        $fresnsModel = User::withTrashed()->where('username', $fsid)->first();
                    }
                break;

                // group
                case 'group':
                    $fresnsModel = Group::withTrashed()->with(['creator', 'admins'])->where('gid', $fsid)->first();
                break;

                // hashtag
                case 'hashtag':
                    $fresnsModel = Hashtag::withTrashed()->where('slug', $fsid)->first();
                break;

                // post
                case 'post':
                    $fresnsModel = Post::withTrashed()->with(['postAppend', 'creator', 'group', 'hashtags'])->where('pid', $fsid)->first();
                break;

                // comment
                case 'comment':
                    $fresnsModel = Comment::withTrashed()->with(['commentAppend', 'post', 'postAppend', 'creator', 'hashtags'])->where('cid', $fsid)->first();
                break;

                // file
                case 'file':
                    $fresnsModel = File::withTrashed()->where('fid', $fsid)->first();
                break;

                // extend
                case 'extend':
                    $fresnsModel = Extend::withTrashed()->where('eid', $fsid)->first();
                break;

                // archive
                case 'archive':
                    $fresnsModel = Archive::withTrashed()->where('code', $fsid)->first();
                break;

                // default
                default:
                    throw new \RuntimeException("unknown modelName {$modelName}");
                break;
            }

            CacheHelper::put($fresnsModel, $cacheKey, $cacheTags);
        }

        return $fresnsModel;
    }

    // get model by id
    public static function fresnsModelById(string $modelName, ?string $id = null)
    {
        if (empty($id) || $id == 0) {
            return null;
        }

        $cacheKey = "fresns_model_{$modelName}_{$id}";
        $cacheTags = match ($modelName) {
            'account' => ['fresnsAccounts', 'fresnsAccountModels'],
            'user' => ['fresnsUsers', 'fresnsUserModels'],
            'group' => ['fresnsGroups', 'fresnsGroupModels'],
            'hashtag' => ['fresnsHashtags', 'fresnsHashtagModels'],
            'post' => ['fresnsPosts', 'fresnsPostModels'],
            'comment' => ['fresnsComments', 'fresnsCommentModels'],
            'file' => ['fresnsModels', 'fresnsFiles'],
            'extend' => ['fresnsModels', 'fresnsExtends'],
            'operation' => ['fresnsModels', 'fresnsOperations'],
            'archive' => ['fresnsModels', 'fresnsArchives'],
            'conversation' => ['fresnsModels', 'fresnsConversations'],
            default => 'fresnsModels',
        };

        // is known to be empty
        $isKnownEmpty = CacheHelper::isKnownEmpty($cacheKey);
        if ($isKnownEmpty) {
            return null;
        }

        $fresnsModel = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($fresnsModel)) {
            switch ($modelName) {
                // account
                case 'account':
                    $fresnsModel = Account::withTrashed()->with(['users', 'connects'])->where('id', $id)->first();
                break;

                // user
                case 'user':
                    $fresnsModel = User::withTrashed()->where('id', $id)->first();
                break;

                // group
                case 'group':
                    $fresnsModel = Group::withTrashed()->with(['creator', 'admins'])->where('id', $id)->first();
                break;

                // hashtag
                case 'hashtag':
                    $fresnsModel = Hashtag::withTrashed()->where('id', $id)->first();
                break;

                // post
                case 'post':
                    $fresnsModel = Post::withTrashed()->with(['postAppend', 'creator', 'group', 'hashtags'])->where('id', $id)->first();
                break;

                // comment
                case 'comment':
                    $fresnsModel = Comment::withTrashed()->with(['commentAppend', 'post', 'postAppend', 'creator', 'hashtags'])->where('id', $id)->first();
                break;

                // file
                case 'file':
                    $fresnsModel = File::withTrashed()->where('id', $id)->first();
                break;

                // extend
                case 'extend':
                    $fresnsModel = Extend::withTrashed()->where('id', $id)->first();
                break;

                // operation
                case 'operation':
                    $fresnsModel = Operation::withTrashed()->where('id', $id)->first();
                break;

                // archive
                case 'archive':
                    $fresnsModel = Archive::withTrashed()->where('id', $id)->first();
                break;

                // conversation
                case 'conversation':
                    $fresnsModel = Conversation::withTrashed()->with(['aUser', 'bUser', 'latestMessage'])->where('id', $id)->first();
                break;

                // default
                default:
                    throw new \RuntimeException("unknown modelName {$modelName}");
                break;
            }

            CacheHelper::put($fresnsModel, $cacheKey, $cacheTags);
        }

        return $fresnsModel;
    }

    // get conversation model
    public static function fresnsModelConversation(int $authUserId, int $conversationUserId)
    {
        $cacheKey = "fresns_model_conversation_{$authUserId}_{$conversationUserId}";
        $cacheTags = ['fresnsUsers', 'fresnsUserConversations'];

        $conversationModel = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($conversationModel)) {
            $aConversation = Conversation::where('a_user_id', $conversationUserId)->where('b_user_id', $authUserId)->first();
            $bConversation = Conversation::where('b_user_id', $conversationUserId)->where('a_user_id', $authUserId)->first();

            if (empty($aConversation) && empty($bConversation)) {
                $conversationColumn['a_user_id'] = $authUserId;
                $conversationColumn['b_user_id'] = $conversationUserId;

                $conversationModel = Conversation::create($conversationColumn);
            } elseif (empty($aConversation)) {
                $conversationModel = $bConversation;
            } else {
                $conversationModel = $aConversation;
            }

            CacheHelper::put($conversationModel, $cacheKey, $cacheTags);
        }

        return $conversationModel;
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

    // get follow model by type
    public static function fresnsFollowModelByType(string $type, int $id, ?int $authUserId = null)
    {
        if (empty($authUserId)) {
            return null;
        }

        $cacheKey = "fresns_follow_{$type}_model_{$id}_by_{$authUserId}";
        $cacheTags = match ($type) {
            'user' => ['fresnsUsers', 'fresnsUserInteractions', 'fresnsFollowData'],
            'group' => ['fresnsGroups', 'fresnsGroupData', 'fresnsUsers', 'fresnsUserInteractions', 'fresnsFollowData'],
            'hashtag' => ['fresnsHashtags', 'fresnsHashtagData', 'fresnsUsers', 'fresnsUserInteractions', 'fresnsFollowData'],
            'post' => ['fresnsPosts', 'fresnsPostData', 'fresnsUsers', 'fresnsUserInteractions', 'fresnsFollowData'],
            'comment' => ['fresnsComments', 'fresnsCommentData', 'fresnsUsers', 'fresnsUserInteractions', 'fresnsFollowData'],
        };

        // is known to be empty
        $isKnownEmpty = CacheHelper::isKnownEmpty($cacheKey);
        if ($isKnownEmpty) {
            return null;
        }

        $fresnsModel = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($fresnsModel)) {
            $followType = match ($type) {
                'user' => UserFollow::TYPE_USER,
                'group' => UserFollow::TYPE_GROUP,
                'hashtag' => UserFollow::TYPE_HASHTAG,
                'post' => UserFollow::TYPE_POST,
                'comment' => UserFollow::TYPE_COMMENT,
                default => null,
            };

            if (empty($followType)) {
                throw new \RuntimeException("unknown type {$type}");
            }

            $fresnsModel = UserFollow::where('user_id', $authUserId)->where('follow_type', $followType)->where('follow_id', $id)->first();

            CacheHelper::put($fresnsModel, $cacheKey, $cacheTags);
        }

        return $fresnsModel;
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
     * @param  string  $userId
     * @return int |null
     */
    public static function fresnsAccountIdByUserId(?string $userId = null)
    {
        if (empty($userId)) {
            return null;
        }

        return PrimaryHelper::fresnsModelByFsid('user', $userId)?->account_id;
    }

    /**
     * @param  string  $uidOrUsername
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

    /**
     * @param  string  $code
     * @return int |null
     */
    public static function fresnsArchiveIdByCode(?string $code = null)
    {
        if (empty($code)) {
            return null;
        }

        return PrimaryHelper::fresnsModelByFsid('archive', $code)?->id;
    }
}
