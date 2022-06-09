<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Utilities;

use App\Helpers\ConfigHelper;
use App\Models\BlockWord;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;

class ValidationUtility
{
    // Validation username
    public static function validUsername(string $username): array
    {
        $config = ConfigHelper::fresnsConfigByItemKeys([
            'username_min',
            'username_max',
            'ban_names',
        ]);
        $length = Str::length($username);
        $user = User::withTrashed()->where('username', $username)->first();

        $formatString = true;
        $isError = preg_match('/^[A-Za-z0-9-]+$/', $username);
        if (! $isError) {
            $formatString = false;
        }

        $formatHyphen = true;
        $hyphenCount = substr_count($username, '-');
        $hyphenStrStart = str_starts_with($username, '-');
        $hyphenStrEnd = str_ends_with($username, '-');
        if ($hyphenCount > 1 || $hyphenStrStart || $hyphenStrEnd) {
            $formatHyphen = false;
        }

        $formatNumeric = true;
        $isNumeric = is_numeric($username);
        if ($isNumeric) {
            $formatNumeric = false;
        }

        $minLength = true;
        if ($length < $config['username_min']) {
            $minLength = false;
        }

        $maxLength = true;
        if ($length > $config['username_max']) {
            $maxLength = false;
        }

        $use = true;
        if (! empty($user)) {
            $use = false;
        }

        $banName = true;
        $newBanNames = array_map('strtolower', $config['ban_names']);
        $isBanName = Str::contains(Str::lower($username), $newBanNames);
        if ($isBanName) {
            $banName = false;
        }

        $validUsername = [
            'formatString' => $formatString,
            'formatHyphen' => $formatHyphen,
            'formatNumeric' => $formatNumeric,
            'minLength' => $minLength,
            'maxLength' => $maxLength,
            'use' => $use,
            'banName' => $banName,
        ];

        return $validUsername;
    }

    // Validation nickname
    public static function validNickname(string $nickname): array
    {
        $config = ConfigHelper::fresnsConfigByItemKeys([
            'nickname_min',
            'nickname_max',
        ]);
        $length = Str::length($nickname);

        $formatString = true;
        $isError = preg_match('/^[\x{4e00}-\x{9fa5} A-Za-z0-9]+$/u', $nickname);
        if (! $isError) {
            $formatString = false;
        }

        $formatSpace = true;
        $spaceCount = substr_count($nickname, ' ');
        $spaceStrStart = str_starts_with($nickname, ' ');
        $spaceStrEnd = str_ends_with($nickname, ' ');
        if ($spaceCount > 1 || $spaceStrStart || $spaceStrEnd) {
            $formatSpace = false;
        }

        $minLength = true;
        if ($length < $config['username_min']) {
            $minLength = false;
        }

        $maxLength = true;
        if ($length > $config['username_max']) {
            $maxLength = false;
        }

        $banName = true;
        $banNames = BlockWord::where('user_mode', 3)->pluck('word')->toArray();
        $newBanNames = array_map('strtolower', $banNames);
        $isBanName = Str::contains(Str::lower($nickname), $newBanNames);
        if ($isBanName) {
            $banName = false;
        }

        $validNickname = [
            'formatString' => $formatString,
            'formatSpace' => $formatSpace,
            'minLength' => $minLength,
            'maxLength' => $maxLength,
            'banName' => $banName,
        ];

        return $validNickname;
    }

    // Validation password
    public static function validPassword(string $password): array
    {
        $config = ConfigHelper::fresnsConfigByItemKeys([
            'password_length',
            'password_strength',
        ]);

        $passwordLength = Str::length($password);

        $length = true;
        if ($passwordLength < $config['password_length']) {
            $length = false;
        }

        $number = true;
        if (in_array(1, $config['password_strength'])) {
            $number = preg_match('/\d/is', $password);
        }

        $lowercase = true;
        if (in_array(2, $config['password_strength'])) {
            $lowercase = preg_match('/[a-z]/', $password);
        }

        $uppercase = true;
        if (in_array(3, $config['password_strength'])) {
            $uppercase = preg_match('/[A-Z]/', $password);
        }

        $symbols = true;
        if (in_array(4, $config['password_strength'])) {
            $symbols = preg_match('/^[A-Za-z0-9]+$/', $password);
        }

        $validPassword = [
            'length' => $length,
            'number' => $number,
            'lowercase' => $lowercase,
            'uppercase' => $uppercase,
            'symbols' => $symbols,
        ];

        return $validPassword;
    }

    // validation user mark
    public static function validUserMarkOwn(int $userId, int $markType, int $markId): bool
    {
        if (! is_numeric($markType)) {
            $markType = match ($markType) {
                default => null,
                'user' => 1,
                'group' => 2,
                'hashtag' => 3,
                'post' => 4,
                'comment' => 5,
            };
        }

        if ($markType == InteractiveUtility::TYPE_USER && $userId == $markId) {
            return false;
        }

        if ($markType == InteractiveUtility::TYPE_POST || $markType == InteractiveUtility::TYPE_COMMENT) {
            switch ($markType) {
                case InteractiveUtility::TYPE_POST:
                    $creator = Post::where('id', $markId)->value('user_id');
                break;

                case InteractiveUtility::TYPE_COMMENT:
                    $creator = Comment::where('id', $markId)->value('user_id');
                break;
            }

            if ($creator == $userId) {
                return false;
            }
        }

        return true;
    }

    // Validation content
    public static function validContent(string $content): array
    {
        $blockWords = BlockWord::whereIn('content_mode', [2, 3, 4])->get();

        $validContent = [
            'validStatus' => true, // true 验证通过，false 有禁止发布的词 (content_mode = 3)
            'reviewStatus' => true, // true 验证通过，false 有需要审核的词 (content_mode = 4)
            'content' => 'content', // 替换词之后的内容 (content_mode = 2)
        ];

        return $validContent;
    }

    // Validation message
    public static function validMessage(string $message): array
    {
        $blockWords = BlockWord::whereIn('dialog_mode', [2, 3])->get();

        $validContent = [
            'validStatus' => true, // true 验证通过，false 有禁止发送的词(dialog_mode = 3)
            'message' => 'message', // 替换词之后的消息 (dialog_mode = 2)
        ];

        return $validContent;
    }
}
