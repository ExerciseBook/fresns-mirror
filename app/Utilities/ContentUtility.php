<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use App\Models\User;
use App\Models\Mention;
use App\Models\Sticker;
use App\Models\DomainLink;
use Illuminate\Support\Collection;

class ContentUtility
{
    public static function getRegexpBy($type)
    {
        return match ($type) {
            'hash' => "/#(.*?)#/",
            'space' => "/#(.*?)\s/",
            'url' => "/(https?:\/\/.*?)\s/",
            'at' => "/@(.*?)\s/",
            'sticker' => "/\[(.*?)\]/",
        };
    }

    public static function filterChars($data, $exceptChars = ',# ')
    {
        $data = array_filter($data);
        // 需要排除的字符数组
        $exceptChars = str_split($exceptChars);

        $result = [];
        foreach ($data as $item) {
            $needExcludeflag = false;
            // 当包含需要排除的字符时，跳过
            foreach ($exceptChars as $char) {
                if (str_contains($item, $char)) {
                    $needExcludeflag = true;
                    break;
                }
            }

            if ($needExcludeflag) {
                continue;
            }

            $result[] = $item;
        }

        return $result;
    }

    public static function matchAll($regexp, $content, ?callable $filterChars = null)
    {
        // 匹配信息在尾部的处理
        $content = $content . ' ';

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
        // 以 # 号开始（开始 # 号后面不支持空格）
        // 以 # 号或者空格结尾
        // 不支持标点符号，含有标点符号不符合要求
        $hashData = ContentUtility::filterChars(
            ContentUtility::matchAll(ContentUtility::getRegexpBy('hash'), $content)
        );
        $spaceData = ContentUtility::filterChars(
            ContentUtility::matchAll(ContentUtility::getRegexpBy('space'), $content)
        );

        // 对提取的话题进行去重处理
        $data = array_unique([...$spaceData, ...$hashData]);

        return $data;
    }

    // Extract url(link)
    public static function extractUrl(string $content): array
    {
        // 以 http:// 或 https:// 开头，以空格结束
        return ContentUtility::matchAll(ContentUtility::getRegexpBy('url'), $content);
    }

    // Extract mention user
    public static function extractMention(string $content): array
    {
        // 以 @ 符号开头，空格结尾
        return ContentUtility::matchAll(ContentUtility::getRegexpBy('at'), $content);
    }

    // Extract sticker
    public static function extractSticker(string $content): array
    {
        // 以 [ 开头，以 ] 结尾
        // 中间不能有空格
        return ContentUtility::filterChars(
            ContentUtility::matchAll(ContentUtility::getRegexpBy('sticker'), $content),
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
        foreach ($hashtagList as $hashTag) {
            if ($config['hashtag_show'] == 1) {
                // 格式 <a href="https://abc.com/hashtag/PHP%E8%AF%AD%E8%A8%80" class="fresns_hashtag" target="_blank">#PHP语言</a>
                $topic = "#{$hashTag}";
                $replaceList[] = "$topic ";
            } else {
                // 格式 <a href="https://abc.com/hashtag/PHP%E8%AF%AD%E8%A8%80" class="fresns_hashtag" target="_blank">#PHP语言#</a>
                $topic = "#{$hashTag}#";
                $replaceList[] = "$topic";
            }

            $link = sprintf(
                '<a href="%s/hashtag/%s" class="fresns_hashtag" target="_blank">%s</a>',
                $config['site_domain'],
                StrHelper::slug($hashTag),
                $topic
            );

            $linkList[] = $link;
        }

        return str_replace($replaceList, $linkList, $content);
    }

    // Replace url
    public static function replaceUrl(string $content): string
    {
        $urlList = ContentUtility::extractUrl($content);

        $urlDataList = DomainLink::whereIn('link_url', $urlList)->get();

        $replaceList = [];
        $linkList = [];
        foreach ($urlList as $url) {
            if ($urlData = $urlDataList->where('', $url)->first()) {
                // 格式 <a href="https://tangjie.me" class="fresns_link" target="_blank">$urlDataList->link_title</a>
                $name = $urlData->link_title;
            } else {
                // 格式 <a href="https://tangjie.me" class="fresns_link" target="_blank">https://tangjie.me</a>
                $name = $url;
            }

            $replaceList[] = "{$url} ";
            $linkList[] = sprintf('<a href="%s" class="fresns_link" target="_blank">%s</a>', $url, $name);
        }

        return str_replace($replaceList, $linkList, $content);
    }

    // Replace mention
    public static function replaceMention(string $content, int $linkedType, int $linkedId): string
    {
        $config = ConfigHelper::fresnsConfigByItemKeys(['site_domain', 'user_identifier']);
        $userList = ContentUtility::extractMention($content);

        $userDataList = User::whereIn('username', $userList)->get();
        /** @var Collection $mentionUserId */
        $mentionUserId = Mention::where('linked_type', $linkedType)->where('linked_id', $linkedId)->get();

        // xxx: 不能直接比对，需要通过集合查数据方式处理
        // 1. 获取所有用户名
        // 2. 更具用户昵称查找到当前用户
        // 3. 根据查找到的用户 id 去帖子被 at 列表差是否有记录。无记录说明用户改过昵称，查到的昵称不是之前被 at 时的昵称
        // userDataList['userId'] == $mentionUserId['mention_user_id']

        $linkList = [];
        $replaceList = [];
        foreach ($userList as $username) {
            $replaceList[] = "@{$username} ";

            // 用户已改昵称，userid 对不上之前被 at 的用户
            $user = $userDataList->where('username')->first();
            $mentionUser = $mentionUserId->where('mention_user_id', $user?->id)->first();

            if (is_null($mentionUser)) {
                // todo: 用户改名后的提示页面链接
                $linkList[] = sprintf('<a href="%s/u/404" class="fresns_user" target="_blank">@%s</a>', $config['site_domain'], $username);
                continue;
            }

            if ($config['user_identifier'] == 'uid') {
                // 格式 <a href="https://abc.com/u/{uid}" class="fresns_user" target="_blank">@昵称</a>
                $name = $user->uid;
            } else {
                // 格式 <a href="https://abc.com/u/{username}" class="fresns_user" target="_blank">@昵称</a>
                $name = $user->username;
            }

            $linkList[] = sprintf('<a href="%s/u/%s" class="fresns_user" target="_blank">@%s</a>', $config['site_domain'], $name, $username);
        }

        return str_replace($replaceList, $linkList, $content);
    }

    // Replace sticker
    public static function replaceSticker(string $content): string
    {
        $stickerList = ContentUtility::extractMention($content);
        $stickerDataList = Sticker::whereIn('code', $stickerList)->get();

        $replaceList = [];
        $linkList = [];
        foreach ($stickerList as $sticker) {
            $replaceList[] = "[$sticker]";

            $currentSticker = $stickerDataList->where('code', $sticker)->first();
            if (is_null($currentSticker)) {
                $linkList[] = "[$sticker]";
            } else {
                $stickerUrl = FileHelper::fresnsFileUrlByTableColumn($sticker->image_file_id, $sticker->image_file_url);

                // 格式 <img src="$stickerUrl" class="fresns_sticker" alt="$sticker->code">
                $linkList[] = sprintf('<img src="%s" class="fresns_sticker" alt="%s" />', $stickerUrl, $currentSticker->code);
            }
        }

        return str_replace($replaceList, $linkList, $content);
    }

    // Content
    public static function contentHandle(string $content, ?int $linkedType = null, ?int $linkedId = null): string
    {
        // Replace hashtag
        // Replace url
        // Replace mention
        // Replace sticker
        $content = static::replaceHashtag($content);
        $content = static::replaceUrl($content);
        if ($linkedType && $linkedId) {
            $content = static::replaceMention($content, $linkedType, $linkedId);
        }
        $content = static::replaceSticker($content);

        return $content;
    }
}
