<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use App\Models\DomainLink;
use App\Models\Mention;
use App\Models\User;

class ContentUtility
{
    // Extract hashtag
    public static function extractHashtag(string $content): array
    {
        // 话题在尾部的处理
        $content = $content . ' ';
        // xxx: 话题中不包含的字符(要过滤的字符)
        $exceptChars = ",# ";
    
        // 获取匹配正则
        $getRregexp = function ($matchType = 'hash') {
            return match ($matchType) {
                'hash' => "/#(.*?)#/",
                'space' => "/#(.*?)\s/",
            };
        };
    
        // 过滤话题中的字符
        $filterChars = function ($data, $exceptChars = ',') {
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
        };
    
        $matchAll = function ($regexp, $content, $exceptChars) use ($filterChars) {
            preg_match_all($regexp, $content, $matches);
    
            $data = $matches[1];
    
            return $filterChars($data, $exceptChars);
        };
    
        // 以 # 号开始（开始 # 号后面不支持空格）
        // 以 # 号或者空格结尾
        // 不支持标点符号，含有标点符号不符合要求
        $hashData = $matchAll($getRregexp('hash'), $content, $exceptChars);
        $spaceData = $matchAll($getRregexp('space'), $content, $exceptChars);

        // 对提取的话题进行去重处理
        $data = array_unique([...$spaceData, ...$hashData]);
    
        return $data;
    }

    // Extract url(link)
    public static function extractUrl(string $content): array
    {
        // 以 http:// 或 https:// 开头，以空格结束

        return [];
    }

    // Extract mention user
    public static function extractMention(string $content): array
    {
        // 以 @ 符号开头，空格结尾

        return [];
    }

    // Extract sticker
    public static function extractSticker(string $content): array
    {
        // 以 [ 开头，以 ] 结尾
        // 中间不能有空格

        return [];
    }

    // Replace hashtag
    public static function replaceHashtag(string $content): string
    {
        $hashtagList = ContentUtility::extractHashtag($content);

        $config = ConfigHelper::fresnsConfigByItemKeys(['site_domain', 'hashtag_show']);

        if ($config['hashtag_show'] == 1) {
            // 格式 <a href="https://abc.com/hashtag/PHP%E8%AF%AD%E8%A8%80" class="fresns_hashtag" target="_blank">#PHP语言</a>
        } else {
            // 格式 <a href="https://abc.com/hashtag/PHP%E8%AF%AD%E8%A8%80" class="fresns_hashtag" target="_blank">#PHP语言#</a>
        }

        return '';
    }

    // Replace url
    public static function replaceUrl(string $content): string
    {
        $urlList = ContentUtility::extractUrl($content);

        $urlDataList = DomainLink::whereIn('link_url', $urlList)->get();

        if (empty($urlDataList)) {
            // 格式 <a href="https://tangjie.me" class="fresns_link" target="_blank">https://tangjie.me</a>
        } else {
            // 格式 <a href="https://tangjie.me" class="fresns_link" target="_blank">$urlDataList->link_title</a>
        }

        return '';
    }

    // Replace mention
    public static function replaceMention(string $content, int $linkedType, int $linkedId): string
    {
        $config = ConfigHelper::fresnsConfigByItemKeys(['site_domain', 'user_identifier']);
        $userList = ContentUtility::extractMention($content);
        $userDataList = User::where('username', $userList)->get();
        $mentionUserId = Mention::where('linked_type', $linkedType)->where('linked_id', $linkedId)->get();

        // userDataList['userId'] == $mentionUserId['mention_user_id']

        if ($config['user_identifier'] == 'uid') {
            // 格式 <a href="https://abc.com/u/{uid}" class="fresns_user" target="_blank">@昵称</a>
        } else {
            // 格式 <a href="https://abc.com/u/{username}" class="fresns_user" target="_blank">@昵称</a>
        }

        return '';
    }

    // Replace sticker
    public static function replaceSticker(string $content): string
    {
        $stickerList = ContentUtility::extractMention($content);
        $stickerDataList = Mention::where('code', $stickerList)->get();

        $stickerUrl = FileHelper::fresnsFileImageUrlByColumn($sticker->image_file_id, $sticker->image_file_url);

        // 格式 <img src="$stickerUrl" class="fresns_sticker" alt="$sticker->code">

        return '';
    }

    // Content
    public static function contentHandle(string $content, ?int $linkedType = null, ?int $linkedId = null): string
    {
        // Replace hashtag
        // Replace url
        // Replace mention
        // Replace sticker

        return '';
    }
}
