<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use App\Models\BlockWord;
use App\Exceptions\ApiException;
use Illuminate\Support\Str;

class ValidationUtility
{
    // Validation username
    public static function validUsername(string $username): bool
    {
        $config = ConfigHelper::fresnsConfigByItemKeys([
            'username_edit',
            'username_min',
            'username_max',
            'ban_names',
        ]);

        // 只允许字母、数字和单个连字符
        // 数字不分前后，但不能是纯数字
        // 连字符只能在中间，不能在开头或结尾，也不能纯是连字符

        // 35101 指定天数内只能修改一次
        // throw new ApiException(35101);

        // 35102 用户名格式错误，请勿使用特殊字符
        // throw new ApiException(35102);

        // 35103 用户名长度超出限制
        // throw new ApiException(35103);

        // 35104 用户名未达到最小长度要求
        // throw new ApiException(35104);

        // 35105 用户名已被使用
        // throw new ApiException(35105);

        // 35106 用户名存在禁用词
        // throw new ApiException(35106);

        return true;
    }

    // Validation nickname
    public static function validNickname(string $nickname): bool
    {
        $config = ConfigHelper::fresnsConfigByItemKeys([
            'nickname_min',
            'nickname_max',
            'nickname_edit',
        ]);

        $blockWords = BlockWord::whereIn('user_mode', [2, 3])->get();

        // 不能带标点符号或特殊符号
        // 允许有单个空格，但空格不能在开头或结尾

        // 35107 昵称格式错误，请勿使用特殊字符
        // throw new ApiException(35107);

        // 35108 昵称长度超出限制
        // throw new ApiException(35108);

        // 35109 昵称未达到最小长度要求
        // throw new ApiException(35109);

        // 35110 昵称存在禁用词
        // throw new ApiException(35110);

        return true;
    }

    // Validation password
    public static function validPassword(string $password): bool
    {
        $config = ConfigHelper::fresnsConfigByItemKeys([
            'password_length',
            'password_strength',
        ]);
        // 1 密码中必须含有数字
        // 2 密码中必须含有小写字母
        // 3 密码中必须含有大写字母
        // 4 密码中必须含有特殊字符（除空格）

        // 34104 密码长度不正确
        // throw new ApiException(34104);

        // 34105 密码应包含数字
        // throw new ApiException(34105);

        // 34106 密码应包含小写字母
        // throw new ApiException(34106);

        // 34107 密码应包含大写字母
        // throw new ApiException(34107);

        // 34108 密码应包含符号（除空格）
        // throw new ApiException(34108);

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
