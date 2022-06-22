<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Utilities;

use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\FileHelper;
use App\Helpers\LanguageHelper;
use App\Helpers\PluginHelper;
use App\Helpers\PrimaryHelper;
use App\Helpers\StrHelper;
use App\Models\ArchiveUsage;
use App\Models\Comment;
use App\Models\CommentAppend;
use App\Models\CommentLog;
use App\Models\Domain;
use App\Models\DomainLink;
use App\Models\DomainLinkUsage;
use App\Models\Extend;
use App\Models\ExtendUsage;
use App\Models\FileUsage;
use App\Models\Group;
use App\Models\Hashtag;
use App\Models\HashtagUsage;
use App\Models\Language;
use App\Models\Mention;
use App\Models\OperationUsage;
use App\Models\Post;
use App\Models\PostAllow;
use App\Models\PostAppend;
use App\Models\PostLog;
use App\Models\Role;
use App\Models\Sticker;
use App\Models\User;

class ContentUtility
{
    // preg regexp
    public static function getRegexpByType($type)
    {
        return match ($type) {
            'hash' => '/#(.*?)#/',
            'space' => "/#(.*?)\s/",
            'url' => "/(https?:\/\/.*?)\s/",
            'at' => "/@(.*?)\s/",
            'sticker' => "/\[(.*?)\]/",
        };
    }

    // Not valid for hashtag containing special characters
    public static function filterChars($data, $exceptChars = ',# ')
    {
        $data = array_filter($data);

        // Array of characters to be excluded
        $exceptChars = str_split($exceptChars);

        $result = [];
        foreach ($data as $item) {
            $needExcludeFlag = false;

            // Skip when it contains characters that need to be excluded
            foreach ($exceptChars as $char) {
                if (str_contains($item, $char)) {
                    $needExcludeFlag = true;
                    break;
                }
            }

            if ($needExcludeFlag) {
                continue;
            }

            $result[] = $item;
        }

        return $result;
    }

    // match all extract
    public static function matchAll($regexp, $content, ?callable $filterChars = null)
    {
        // Matching information is handled at the end
        $content = $content.' ';

        preg_match_all($regexp, $content, $matches);

        $data = $matches[1] ?? [];

        if (is_callable($filterChars)) {
            return $filterChars($data);
        }

        return $data;
    }

    // Extract hashtag
    public static function extractHashtag(string $content): array
    {
        $hashData = ContentUtility::filterChars(
            ContentUtility::matchAll(ContentUtility::getRegexpByType('hash'), $content)
        );
        $spaceData = ContentUtility::filterChars(
            ContentUtility::matchAll(ContentUtility::getRegexpByType('space'), $content)
        );

        // De-duplication of the extracted hashtag
        $data = array_unique([...$spaceData, ...$hashData]);

        return $data;
    }

    // Extract link
    public static function extractLink(string $content): array
    {
        return ContentUtility::matchAll(ContentUtility::getRegexpByType('url'), $content);
    }

    // Extract mention user
    public static function extractMention(string $content): array
    {
        return ContentUtility::matchAll(ContentUtility::getRegexpByType('at'), $content);
    }

    // Extract sticker
    public static function extractSticker(string $content): array
    {
        return ContentUtility::filterChars(
            ContentUtility::matchAll(ContentUtility::getRegexpByType('sticker'), $content),
            ' '
        );
    }

    // Replace hashtag
    public static function replaceHashtag(string $content): string
    {
        $hashtagList = ContentUtility::extractHashtag($content);

        $config = ConfigHelper::fresnsConfigByItemKeys(['site_domain', 'hashtag_show']);

        $replaceList = [];
        $linkList = [];
        foreach ($hashtagList as $hashtagName) {
            if ($config['hashtag_show'] == 1) {
                // <a href="https://abc.com/hashtag/PHP" class="fresns_hashtag" target="_blank">#PHP</a>
                $hashtag = "#{$hashtagName}";
                $replaceList[] = "$hashtag ";
            } else {
                // <a href="https://abc.com/hashtag/PHP" class="fresns_hashtag" target="_blank">#PHP#</a>
                $hashtag = "#{$hashtagName}#";
                $replaceList[] = "$hashtag";
            }

            $link = sprintf(
                '<a href="%s/hashtag/%s" class="fresns_hashtag" target="_blank">%s</a>',
                $config['site_domain'],
                StrHelper::slug($hashtagName),
                $hashtag
            );

            $linkList[] = $link;
        }

        return str_replace($replaceList, $linkList, $content);
    }

    // Replace link
    public static function replaceLink(string $content): string
    {
        $urlList = ContentUtility::extractLink($content);

        $urlDataList = DomainLink::whereIn('link_url', $urlList)->get();

        $replaceList = [];
        $linkList = [];
        foreach ($urlList as $url) {
            $urlData = $urlDataList->where('link_url', $url)->first();

            // <a href="https://fresns.org" class="fresns_link" target="_blank">Fresns Website</a>
            // or
            // <a href="https://fresns.org" class="fresns_link" target="_blank">https://fresns.org</a>
            $title = $urlData->link_title ?? $url;

            $replaceList[] = "{$url} ";
            $linkList[] = sprintf('<a href="%s" class="fresns_link" target="_blank">%s</a>', $url, $title);
        }

        return str_replace($replaceList, $linkList, $content);
    }

    // Replace mention
    public static function replaceMention(string $content, int $mentionType, int $mentionId): string
    {
        $config = ConfigHelper::fresnsConfigByItemKeys(['site_domain', 'user_identifier']);
        $usernameList = ContentUtility::extractMention($content);

        $userData = User::whereIn('username', $usernameList)->get();
        $mentionData = Mention::where('mention_type', $mentionType)->where('mention_id', $mentionId)->get();

        $linkList = [];
        $replaceList = [];
        foreach ($usernameList as $username) {
            // check mention record
            $user = $userData->where('username', $username)->first();
            $mentionUser = $mentionData->where('mention_user_id', $user?->id)->first();

            if (is_null($mentionUser)) {
                $replaceList[] = "@{$username} ";
                $linkList[] = sprintf('<a href="%s/u/404" class="fresns_user" target="_blank">@%s</a>', $config['site_domain'], $username);
                continue;
            }

            if ($config['user_identifier'] == 'uid') {
                // <a href="https://abc.com/u/{uid}" class="fresns_user" target="_blank">@nickname</a>
                $urlName = $user->uid;
            } else {
                // <a href="https://abc.com/u/{username}" class="fresns_user" target="_blank">@nickname</a>
                $urlName = $user->username;
            }

            $replaceList[] = "@{$user->nickname} ";

            $linkList[] = sprintf('<a href="%s/u/%s" class="fresns_user" target="_blank">@%s</a>', $config['site_domain'], $urlName, $user->nickname);
        }

        return str_replace($replaceList, $linkList, $content);
    }

    // Replace sticker
    public static function replaceSticker(string $content): string
    {
        $stickerCodeList = ContentUtility::extractMention($content);
        $stickerDataList = Sticker::whereIn('code', $stickerCodeList)->get();

        $replaceList = [];
        $linkList = [];
        foreach ($stickerCodeList as $sticker) {
            $replaceList[] = "[$sticker]";

            $currentSticker = $stickerDataList->where('code', $sticker)->first();
            if (is_null($currentSticker)) {
                $linkList[] = "[$sticker]";
            } else {
                $stickerUrl = FileHelper::fresnsFileUrlByTableColumn($sticker->image_file_id, $sticker->image_file_url);

                // <img src="$stickerUrl" class="fresns_sticker" alt="$sticker->code">
                $linkList[] = sprintf('<img src="%s" class="fresns_sticker" alt="%s" />', $stickerUrl, $currentSticker->code);
            }
        }

        return str_replace($replaceList, $linkList, $content);
    }

    // Handle and replace all
    public static function handleAndReplaceAll(string $content, ?int $mentionType = null, ?int $mentionId = null): string
    {
        // Replace hashtag
        // Replace link
        // Replace mention
        // Replace sticker
        $content = static::replaceHashtag($content);
        $content = static::replaceLink($content);
        if ($mentionType && $mentionId) {
            $content = static::replaceMention($content, $mentionType, $mentionId);
        }
        $content = static::replaceSticker($content);

        return $content;
    }

    // Save hashtag
    public static function saveHashtag(string $content, int $usageType, int $useId)
    {
        $hashtagArr = ContentUtility::extractHashtag($content);

        // add hashtag data
        foreach ($hashtagArr as $hashtag) {
            Hashtag::firstOrCreate([
                'name' => $hashtag,
            ], [
                'slug' => StrHelper::slug($hashtag),
            ]);
        }

        // add hashtag use
        $hashtagIdArr = Hashtag::whereIn('name', $hashtagArr)->pluck('id')->toArray();

        $hashtagUseData = [];
        foreach ($hashtagIdArr as $hashtagId) {
            $hashtagUseData[] = [
                'usage_type' => $usageType,
                'usage_id' => $useId,
                'hashtag_id' => $hashtagId,
            ];
        }

        HashtagUsage::createMany($hashtagUseData);
    }

    // Save link
    public static function saveLink(string $content, int $usageType, int $useId)
    {
        $urlArr = ContentUtility::extractLink($content);

        // add domain data
        foreach ($urlArr as $url) {
            Domain::firstOrCreate([
                'host' => parse_url($url, PHP_URL_HOST),
            ], [
                'domain' => StrHelper::extractDomainByUrl($url),
            ]);
        }

        // add domain link data
        foreach ($urlArr as $url) {
            DomainLink::firstOrCreate([
                'link_url' => $url,
            ], [
                'domain_id' => Domain::withTrashed()->where('host', parse_url($url, PHP_URL_HOST))->value('id') ?? 0,
            ]);
        }

        // add domain link use
        $urlIdArr = DomainLink::whereIn('link_url', $urlArr)->pluck('id')->toArray();
        $urlUseData = [];
        foreach ($urlIdArr as $urlId) {
            $urlUseData[] = [
                'usage_type' => $usageType,
                'usage_id' => $useId,
                'link_id' => $urlId,
            ];
        }
        DomainLinkUsage::createMany($urlUseData);
    }

    // Save mention user
    public static function saveMention(string $content, int $mentionType, int $mentionId, int $authUserId)
    {
        $usernameArr = ContentUtility::extractMention($content);
        $userIdArr = User::whereIn('username', $usernameArr)->pluck('id')->toArray();

        $mentionData = [];
        foreach ($userIdArr as $userId) {
            $mentionData[] = [
                'user_id' => $authUserId,
                'mention_type' => $mentionType,
                'mention_id' => $mentionId,
                'mention_user_id' => $userId,
            ];
        }

        Mention::createMany($mentionData);
    }

    // Handle and save all(interactive content)
    public static function handleAndSaveAllInteractive(string $content, int $type, int $id, ?int $authUserId = null)
    {
        static::saveHashtag($content, $type, $id);
        static::saveLink($content, $type, $id);

        if (! empty($authUserId)) {
            static::saveMention($content, $type, $id, $authUserId);
        }
    }

    // extend json handle
    public static function extendJsonHandle(array $extends, string $langTag): array
    {
        $extendsCollection = collect($extends);

        $extendArr = Extend::whereIn('eid', $extendsCollection->pluck('eid'))->isEnable()->get();

        $extendList = null;
        foreach ($extendArr as $extend) {
            $item['eid'] = $extend->eid;
            $item['canDelete'] = $extendsCollection->where('eid', $extend->eid)->value('canDelete');
            $item['rating'] = $extendsCollection->where('eid', $extend->eid)->value('rating');
            $item['frameType'] = $extend->frame_type;
            $item['framePosition'] = $extend->frame_position;
            $item['textContent'] = $extend->text_content;
            $item['textIsMarkdown'] = $extend->text_is_markdown;
            $item['cover'] = FileHelper::fresnsFileUrlByTableColumn($extend['cover_file_id'], $extend['cover_file_url']);
            $item['title'] = LanguageHelper::fresnsLanguageByTableId('extends', 'title', $extend->id, $langTag);
            $item['titleColor'] = $extend->title_color;
            $item['descPrimary'] = LanguageHelper::fresnsLanguageByTableId('extends', 'desc_primary', $extend->id, $langTag);
            $item['descPrimaryColor'] = $extend->desc_primary_color;
            $item['descSecondary'] = LanguageHelper::fresnsLanguageByTableId('extends', 'desc_secondary', $extend->id, $langTag);
            $item['descSecondaryColor'] = $extend->desc_secondary_color;
            $item['btnName'] = LanguageHelper::fresnsLanguageByTableId('extends', 'btn_name', $extend->id, $langTag);
            $item['type'] = $extend->extend_type;
            $item['target'] = $extend->extend_target;
            $item['value'] = $extend->extend_value;
            $item['support'] = $extend->extend_support;
            $item['moreJson'] = $extend->more_json;

            $extendList[] = $item;
        }

        return collect($extendList)->sortBy('rating')->toArray();
    }

    // read allow json handle
    public static function readAllowJsonHandle(array $readAllowConfig, string $langTag, string $timezone): array
    {
        $permissions['users'] = null;
        if (empty($readAllowConfig['permissions']['users'])) {
            $users = User::whereIn('uid', $readAllowConfig['permissions']['users'])->first();
            foreach ($users as $user) {
                $userList = $user->getUserProfile($langTag, $timezone);
            }
            $permissions['users'] = $userList;
        }

        $permissions['roles'] = null;
        if (empty($readAllowConfig['permissions']['roles'])) {
            $roles = Role::whereIn('id', $readAllowConfig['permissions']['roles'])->first();
            foreach ($roles as $role) {
                $roleItem['rid'] = $role->id;
                $roleItem['nicknameColor'] = $role->nickname_color;
                $roleItem['name'] = LanguageHelper::fresnsLanguageByTableId('roles', 'name', $role->id, $langTag);
                $roleItem['nameDisplay'] = (bool) $role->is_display_name;
                $roleItem['icon'] = FileHelper::fresnsFileUrlByTableColumn($role->icon_file_id, $role->icon_file_url);
                $roleItem['iconDisplay'] = (bool) $role->is_display_icon;
                $roleItem['status'] = (bool) $role->is_enable;
                $roleList[] = $roleItem;
            }
            $permissions['roles'] = $roleList;
        }

        $item['isAllow'] = (bool) $readAllowConfig['isAllow'];
        $item['proportion'] = $readAllowConfig['proportion'];
        $item['url'] = PluginHelper::fresnsPluginUrlByUnikey($readAllowConfig['pluginUnikey']);
        $item['btnName'] = collect($readAllowConfig['btnName'])->where('langTag', $langTag)->first()['name'] ?? null;
        $item['permissions'] = $permissions;

        return $item;
    }

    // user list json handle
    public static function userListJsonHandle(array $userListConfig, string $langTag): array
    {
        $item['isUserList'] = (bool) $userListConfig['isUserList'];
        $item['userListName'] = collect($userListConfig['userListName'])->where('langTag', $langTag)->first()['name'] ?? null;
        $item['url'] = PluginHelper::fresnsPluginUrlByUnikey($userListConfig['pluginUnikey']);

        return $item;
    }

    // comment btn json handle
    public static function commentBtnJsonHandle(array $commentBtnConfig, string $langTag): array
    {
        $item['isCommentBtn'] = (bool) $commentBtnConfig['isCommentBtn'];
        $item['btnName'] = collect($commentBtnConfig['btnName'])->where('langTag', $langTag)->first()['name'] ?? null;
        $item['btnStyle'] = $commentBtnConfig['btnStyle'];
        $item['url'] = PluginHelper::fresnsPluginUrlByUnikey($commentBtnConfig['pluginUnikey']);

        return $item;
    }

    // save file usages
    // $files = [{"fid": "fid", "rating": 9, "remark": "remark"}]
    public static function saveFileUsages(int $usageType, string $tableName, string $tableColumn, int $tableId, array $files, int $platformId, int $accountId, int $userId)
    {
        foreach ($files as $file) {
            $fileModel = PrimaryHelper::fresnsModelByFsid('file', $file['fid']);

            FileUsage::updateOrCreate([
                'file_id' => $fileModel->id,
                'table_name' => $tableName,
                'table_column' => $tableColumn,
                'table_id' => $tableId,
            ],
            [
                'file_type' => $$fileModel->type,
                'usage_type' => $usageType,
                'platform_id' => $platformId,
                'rating' => $file['rating'],
                'account_id' => $accountId,
                'user_id' => $userId,
                'remark' => $file['remark'],
            ]);
        }
    }

    // save operation usages
    // $operations = [{"id": "id", "pluginUnikey": null}]
    public static function saveOperationUsages(string $usageType, int $usageId, array $operations)
    {
        foreach ($operations as $operation) {
            $operationModel = PrimaryHelper::fresnsModelById('operation', $operation['id']);

            OperationUsage::updateOrCreate([
                'usage_type' => $usageType,
                'usage_id' => $usageId,
                'operation_id' => $operation->id,
            ],
            [
                'plugin_unikey' => $operation['pluginUnikey'] ?? $operationModel->plugin_unikey,
            ]);
        }
    }

    // save archive usages
    // $archives = [{"code": "code", "value": "value", "isPrivate": true, "pluginUnikey": null}]
    public static function saveArchiveUsages(string $usageType, int $usageId, array $archives)
    {
        foreach ($archives as $archive) {
            $archiveModel = PrimaryHelper::fresnsModelByFsid('archive', $archive['code']);

            OperationUsage::updateOrCreate([
                'usage_type' => $usageType,
                'usage_id' => $usageId,
                'archive_id' => $archiveModel->id,
            ],
            [
                'archive_value' => $archive['value'],
                'is_private' => $archive['isPrivate'],
                'plugin_unikey' => $archive['pluginUnikey'] ?? $archiveModel->plugin_unikey,
            ]);
        }
    }

    // save extend usages
    // $extends = [{"eid": "eid", "canDelete": true, "rating": 9, "pluginUnikey": null}]
    public static function saveExtendUsages(string $usageType, int $usageId, array $extends)
    {
        foreach ($extends as $extend) {
            $extendModel = PrimaryHelper::fresnsModelByFsid('extend', $extend['eid']);

            ExtendUsage::updateOrCreate([
                'usage_type' => $usageType,
                'usage_id' => $usageId,
                'extend_id' => $extendModel->id,
            ],
            [
                'can_delete' => $extend['canDelete'],
                'rating' => $extend['rating'],
                'plugin_unikey' => $extend['pluginUnikey'] ?? $extendModel->plugin_unikey,
            ]);
        }
    }

    // release lang name
    public static function releaseLangName(string $tableName, string $tableColumn, int $tableId, array $langContentArr): string
    {
        $defaultLangTag = ConfigHelper::fresnsConfigDefaultLangTag();

        foreach ($langContentArr as $lang) {
            Language::updateOrCreate([
                'table_name' => $tableName,
                'table_column' => $tableColumn,
                'table_id' => $tableId,
                'lang_tag' => $lang['langTag'],
            ],
            [
                'lang_content' => $lang['name'],
            ]);

            if ($lang['langTag'] == $defaultLangTag) {
                $defaultLangName = $lang['name'];
            }
        }

        return $defaultLangName ?? null;
    }

    // release allow users and roles
    public static function releaseAllowUsersAndRoles(int $postId, array $permArr)
    {
        PostAllow::where('post_id', $postId)->where('type', PostAllow::TYPE_USER)->where('is_initial', 1)->delete();

        foreach ($permArr['users'] as $userId) {
            PostAllow::withTrashed()->updateOrCreate([
                'post_id' => $postId,
                'type' => PostAllow::TYPE_USER,
                'object_id' => $userId,
            ],
            [
                'is_initial' => 1,
                'deleted_at' => null,
            ]);
        }

        PostAllow::where('post_id', $postId)->where('type', PostAllow::TYPE_ROLE)->where('is_initial', 1)->delete();

        foreach ($permArr['roles'] as $roleId) {
            PostAllow::withTrashed()->updateOrCreate([
                'post_id' => $postId,
                'type' => PostAllow::TYPE_ROLE,
                'object_id' => $roleId,
            ],
            [
                'is_initial' => 1,
                'deleted_at' => null,
            ]);
        }
    }

    // release file usages
    public static function releaseFileUsages(string $type, int $logId, int $primaryId)
    {
        $logTableName = match ($type) {
            'post' => 'post_logs',
            'comment' => 'comment_logs',
        };

        $tableName = match ($type) {
            'post' => 'posts',
            'comment' => 'comments',
        };

        FileUsage::where('table_name', $tableName)->where('table_column', 'id')->where('table_id', $primaryId)->delete();

        $fileUsages = FileUsage::where('table_name', $logTableName)->where('table_column', 'id')->where('table_id', $logId)->get();

        $fileData = [];
        foreach ($fileUsages as $file) {
            $fileData[] = [
                'file_id' => $file->id,
                'file_type' => $file->file_type,
                'usage_type' => $file->usage_type,
                'platform_id' => $file->platform_id,
                'table_name' => $tableName,
                'table_column' => 'id',
                'table_id' => $primaryId,
                'rating' => $file->rating,
                'account_id' => $file->account_id,
                'user_id' => $file->user_id,
                'remark' => $file->remark,
            ];
        }

        FileUsage::createMany($fileData);
    }

    // release operation usages
    public static function releaseOperationUsages(string $type, int $logId, int $primaryId)
    {
        $logUsageType = match ($type) {
            'post' => OperationUsage::TYPE_POST_LOG,
            'comment' => OperationUsage::TYPE_COMMENT_LOG,
        };

        $usageType = match ($type) {
            'post' => OperationUsage::TYPE_POST,
            'comment' => OperationUsage::TYPE_COMMENT,
        };

        OperationUsage::where('usage_type', $usageType)->where('usage_id', $primaryId)->delete();

        $operationUsages = OperationUsage::where('usage_type', $logUsageType)->where('usage_id', $logId)->get();

        $operationData = [];
        foreach ($operationUsages as $operation) {
            $operationData[] = [
                'usage_type' => $usageType,
                'usage_id' => $primaryId,
                'operation_id' => $operation->operation_id,
                'plugin_unikey' => $operation->plugin_unikey,
            ];
        }

        OperationUsage::createMany($operationData);
    }

    // release archive usages
    public static function releaseArchiveUsages(string $type, int $logId, int $primaryId)
    {
        $logUsageType = match ($type) {
            'post' => ArchiveUsage::TYPE_POST_LOG,
            'comment' => ArchiveUsage::TYPE_COMMENT_LOG,
        };

        $usageType = match ($type) {
            'post' => ArchiveUsage::TYPE_POST,
            'comment' => ArchiveUsage::TYPE_COMMENT,
        };

        ArchiveUsage::where('usage_type', $usageType)->where('usage_id', $primaryId)->delete();

        $archiveUsages = ArchiveUsage::where('usage_type', $logUsageType)->where('usage_id', $logId)->get();

        $archiveData = [];
        foreach ($archiveUsages as $archive) {
            $archiveData[] = [
                'usage_type' => $usageType,
                'usage_id' => $primaryId,
                'archive_id' => $archive->archive_id,
                'archive_value' => $archive->archive_value,
                'is_private' => $archive->is_private,
                'plugin_unikey' => $archive->plugin_unikey,
            ];
        }

        ArchiveUsage::createMany($archiveData);
    }

    // release extend usages
    public static function releaseExtendUsages(int $type, int $logId, int $primaryId)
    {
        $logUsageType = match ($type) {
            'post' => ExtendUsage::TYPE_POST_LOG,
            'comment' => ExtendUsage::TYPE_COMMENT_LOG,
        };

        $usageType = match ($type) {
            'post' => ExtendUsage::TYPE_POST,
            'comment' => ExtendUsage::TYPE_COMMENT,
        };

        ExtendUsage::where('usage_type', $usageType)->where('usage_id', $primaryId)->delete();

        $extendUsages = ExtendUsage::where('usage_type', $logUsageType)->where('usage_id', $logId)->get();

        $extendData = [];
        foreach ($extendUsages as $extend) {
            $extendData[] = [
                'usage_type' => $usageType,
                'usage_id' => $primaryId,
                'extend_id' => $extend->extend_id,
                'can_delete' => $extend->can_delete,
                'rating' => $extend->rating,
                'plugin_unikey' => $extend->plugin_unikey,
            ];
        }

        ExtendUsage::createMany($extendData);
    }

    // release post
    public static function releasePost(PostLog $postLog): Post
    {
        if (! empty($postLog->post_id)) {
            $oldPost = PrimaryHelper::fresnsModelById('post', $postLog->post_id);
        }

        $post = Post::updateOrCreate([
            'id' => $postLog->post_id,
        ],
        [
            'user_id' => $postLog->user_id,
            'group_id' => $postLog->group_id,
            'types' => $postLog->types,
            'title' => $postLog->title,
            'content' => $postLog->content,
            'is_markdown' => $postLog->is_markdown,
            'is_anonymous' => $postLog->is_anonymous,
            'is_comment' => $postLog->is_comment,
            'map_id' => $postLog->location_json['mapId'] ?? null,
            'map_longitude' => $postLog->location_json['latitude'] ?? null,
            'map_latitude' => $postLog->location_json['longitude'] ?? null,
        ]);

        $allowBtnName = null;
        if (empty($postLog->allow_json)) {
            Language::where('table_name', 'post_appends')->where('table_column', 'allow_btn_name')->where('table_id', $post->id)->delete();
        } else {
            $allowBtnName = ContentUtility::releaseLangName('post_appends', 'allow_btn_name', $post->id, $postLog->allow_json['btnName']);
        }

        $userListName = null;
        if (empty($postLog->user_list_json)) {
            Language::where('table_name', 'post_appends')->where('table_column', 'user_list_name')->where('table_id', $post->id)->delete();
        } else {
            $userListName = ContentUtility::releaseLangName('post_appends', 'user_list_name', $post->id, $postLog->user_list_json['userListName']);
        }

        $commentBtnName = null;
        if (empty($postLog->comment_btn_json)) {
            Language::where('table_name', 'post_appends')->where('table_column', 'comment_btn_name')->where('table_id', $post->id)->delete();
        } else {
            $commentBtnName = ContentUtility::releaseLangName('post_appends', 'comment_btn_name', $post->id, $postLog->comment_btn_json['btnName']);
        }

        $postAppend = PostAppend::updateOrCreate([
            'post_id' => $postLog->post_id,
        ],
        [
            'is_plugin_editor' => $postLog->is_plugin_editor,
            'editor_unikey' => $postLog->editor_unikey,
            'is_allow' => $postLog->allow_json['isAllow'] ?? null,
            'allow_proportion' => $postLog->allow_json['proportion'] ?? null,
            'allow_btn_name' => $allowBtnName,
            'allow_plugin_unikey' => $postLog->allow_json['pluginUnikey'] ?? null,
            'is_user_list' => $postLog->user_list_json['isUserList'] ?? null,
            'user_list_name' => $userListName,
            'user_list_plugin_unikey' => $postLog->user_list_json['pluginUnikey'] ?? null,
            'is_comment_btn' => $postLog->comment_btn_json['isCommentBtn'] ?? null,
            'comment_btn_name' => $commentBtnName,
            'comment_btn_style' => $postLog->comment_btn_json['btnStyle'] ?? null,
            'comment_btn_plugin_unikey' => $postLog->comment_btn_json['pluginUnikey'] ?? null,
            'is_comment_public' => $postLog->is_comment_public,
            'map_json' => $postLog->location_json ?? null,
            'map_scale' => $postLog->location_json['scale'] ?? null,
            'map_continent_code' => $postLog->location_json['continentCode'] ?? null,
            'map_country_code' => $postLog->location_json['countryCode'] ?? null,
            'map_region_code' => $postLog->location_json['regionCode'] ?? null,
            'map_city_code' => $postLog->location_json['cityCode'] ?? null,
            'map_city' => $postLog->location_json['city'] ?? null,
            'map_zip' => $postLog->location_json['zip'] ?? null,
            'map_poi' => $postLog->location_json['poi'] ?? null,
            'map_poi_id' => $postLog->location_json['poiId'] ?? null,
        ]);

        ContentUtility::releaseAllowUsersAndRoles($post->id, $postLog->allow_json['permissions']);
        ContentUtility::releaseFileUsages('post', $postLog->id, $post->id);
        ContentUtility::releaseArchiveUsages('post', $postLog->id, $post->id);
        ContentUtility::releaseOperationUsages('post', $postLog->id, $post->id);
        ContentUtility::releaseExtendUsages('post', $postLog->id, $post->id);

        if (empty($postLog->post_id)) {
            ContentUtility::handleAndSaveAllInteractive($postLog->content, Mention::TYPE_POST, $post->id, $postLog->user_id);
            InteractiveUtility::publishStats('post', $post->id, 'increment');
        } else {
            if ($postLog->group_id != $oldPost->group_id) {
                Group::where('id', $oldPost->group_id)->decrement('post_count');
                Group::where('id', $postLog->group_id)->increment('post_count');

                $groupCommentCount = Comment::where('post_id', $post->id)->count();

                Comment::where('post_id', $post->id)->update([
                    'group_id' => $postLog->group_id,
                ]);

                Group::where('id', $postLog->group_id)->increment('comment_count', $groupCommentCount);
                Group::where('id', $oldPost->group_id)->decrement('comment_count', $groupCommentCount);
            }

            InteractiveUtility::editStats('post', $post->id, 'decrement');

            HashtagUsage::where('usage_type', HashtagUsage::TYPE_POST)->where('usage_id', $post->id)->delete();
            DomainLinkUsage::where('usage_type', DomainLinkUsage::TYPE_POST)->where('usage_id', $post->id)->delete();
            Mention::where('user_id', $postLog->user_id)->where('mention_type', Mention::TYPE_POST)->where('mention_id', $post->id)->delete();

            ContentUtility::handleAndSaveAllInteractive($postLog->content, Mention::TYPE_POST, $post->id, $postLog->user_id);
            InteractiveUtility::editStats('post', $post->id, 'increment');

            $post->update([
                'latest_edit_at' => now(),
            ]);
            $postAppend->increment('edit_count');

            CacheHelper::forgetFresnsModel('post', $post->pid);
            CacheHelper::forgetFresnsModel('post', $post->id);
        }

        $postLog->update([
            'post_id' => $post->id,
            'state' => 3,
        ]);

        return $post;
    }

    // release comment
    public static function releaseComment(CommentLog $commentLog): Comment
    {
        $post = PrimaryHelper::fresnsModelById('post', $commentLog->post_id);
        $parentComment = PrimaryHelper::fresnsModelById('comment', $commentLog->parent_id);

        $topCommentId = null;
        if (! $parentComment) {
            $topCommentId = $parentComment?->top_comment_id ?? null;
        }

        $comment = Comment::updateOrCreate([
            'id' => $commentLog->comment_id,
        ],
        [
            'user_id' => $commentLog->user_id,
            'post_id' => $commentLog->post_id,
            'group_id' => $post->group_id,
            'top_comment_id' => $topCommentId,
            'parent_id' => $commentLog->parent_comment_id,
            'types' => $commentLog->types,
            'content' => $commentLog->content,
            'is_markdown' => $commentLog->is_markdown,
            'is_anonymous' => $commentLog->is_anonymous,
            'map_id' => $commentLog->location_json['mapId'] ?? null,
            'map_longitude' => $commentLog->location_json['latitude'] ?? null,
            'map_latitude' => $commentLog->location_json['longitude'] ?? null,
        ]);

        $commentAppend = CommentAppend::updateOrCreate([
            'comment_id' => $commentLog->comment_id,
        ],
        [
            'is_plugin_editor' => $commentLog->is_plugin_editor,
            'editor_unikey' => $commentLog->editor_unikey,
            'map_json' => $commentLog->location_json ?? null,
            'map_scale' => $commentLog->location_json['scale'] ?? null,
            'map_continent_code' => $commentLog->location_json['continentCode'] ?? null,
            'map_country_code' => $commentLog->location_json['countryCode'] ?? null,
            'map_region_code' => $commentLog->location_json['regionCode'] ?? null,
            'map_city_code' => $commentLog->location_json['cityCode'] ?? null,
            'map_city' => $commentLog->location_json['city'] ?? null,
            'map_zip' => $commentLog->location_json['zip'] ?? null,
            'map_poi' => $commentLog->location_json['poi'] ?? null,
            'map_poi_id' => $commentLog->location_json['poiId'] ?? null,
        ]);

        ContentUtility::releaseFileUsages('comment', $commentLog->id, $comment->id);
        ContentUtility::releaseArchiveUsages('comment', $commentLog->id, $comment->id);
        ContentUtility::releaseOperationUsages('comment', $commentLog->id, $comment->id);
        ContentUtility::releaseExtendUsages('comment', $commentLog->id, $comment->id);

        if (empty($commentLog->comment_id)) {
            ContentUtility::handleAndSaveAllInteractive($commentLog->content, Mention::TYPE_COMMENT, $comment->id, $commentLog->user_id);
            InteractiveUtility::publishStats('comment', $comment->id, 'increment');
        } else {
            InteractiveUtility::editStats('comment', $comment->id, 'decrement');

            HashtagUsage::where('usage_type', HashtagUsage::TYPE_COMMENT)->where('usage_id', $comment->id)->delete();
            DomainLinkUsage::where('usage_type', DomainLinkUsage::TYPE_COMMENT)->where('usage_id', $comment->id)->delete();
            Mention::where('user_id', $commentLog->user_id)->where('mention_type', Mention::TYPE_COMMENT)->where('mention_id', $comment->id)->delete();

            ContentUtility::handleAndSaveAllInteractive($commentLog->content, Mention::TYPE_COMMENT, $comment->id, $commentLog->user_id);
            InteractiveUtility::editStats('comment', $comment->id, 'increment');

            $post->update([
                'latest_edit_at' => now(),
            ]);
            $commentAppend->increment('edit_count');

            CacheHelper::forgetFresnsModel('comment', $comment->cid);
            CacheHelper::forgetFresnsModel('comment', $comment->id);
        }

        $commentLog->update([
            'comment_id' => $comment->id,
            'state' => 3,
        ]);

        return $comment;
    }
}
