<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Utilities;

use App\Helpers\ConfigHelper;
use App\Models\BlockWord;
use Illuminate\Support\Str;

class ValidationUtility
{
    // Validation username
    public static function validUsername(string $username): array
    {
        $config = ConfigHelper::fresnsConfigByItemKeys([
            'username_edit',
            'username_min',
            'username_max',
            'ban_names',
        ]);

        $format = true;
        // 只允许字母、数字和单个连字符
        // 数字不分前后，但不能是纯数字
        // 连字符只能在中间，不能在开头或结尾，也不能纯是连字符

        $minLength = true;
        // 用户名长度超出限制

        $maxLength = true;
        // 用户名未达到最小长度要求

        $use = true;
        // 用户名已被使用

        $banName = true;
        // 用户名存在禁用词

        $validUsername = [
            'format' => $format,
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
            'nickname_edit',
        ]);

        $blockWords = BlockWord::whereIn('user_mode', [2, 3])->get();

        $format = true;
        // 不能带标点符号或特殊符号
        // 允许有单个空格，但空格不能在开头或结尾

        $minLength = true;
        // 昵称长度超出限制

        $maxLength = true;
        // 昵称未达到最小长度要求

        $blockWord = true;
        // 昵称存在禁用词

        $validNickname = [
            'format' => $format,
            'minLength' => $minLength,
            'maxLength' => $maxLength,
            'blockWord' => $blockWord,
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

        $length = false;
        if ($passwordLength > $config['password_length']) {
            $length = true;
        }

        $number = true;
        if (in_array(1, $config['password_strength'])) {
            $number = preg_match('/\d/is', $password);
        }

        $lowercase = true;
        if (in_array(1, $config['password_strength'])) {
            $lowercase = preg_match('/[a-z]/', $password);
        }

        $uppercase = true;
        if (in_array(1, $config['password_strength'])) {
            $uppercase = preg_match('/[A-Z]/', $password);
        }

        $symbols = true;
        if (in_array(1, $config['password_strength'])) {
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
