<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Fresns Installation Language Lines
    |--------------------------------------------------------------------------
    */

    // header
    'title' => '安装',
    'desc' => '安装向导',
    // step 1
    'step1Title' => '欢迎使用 Fresns',
    'step1Desc' => '在开始前，我们需要您数据库的一些信息。请准备好如下信息。',
    'step1DatabaseName' => '数据库名',
    'step1DatabaseUsername' => '数据库用户名',
    'step1DatabasePassword' => '数据库密码',
    'step1DatabaseHost' => '数据库主机',
    'step1DatabaseTablePrefix' => '数据表前缀（table prefix，特别是当您要在一个数据库中安装多个 Fresns 时）',
    'step1DatabaseDesc' => '绝大多数时候，您的主机服务提供商会给您这些信息。如果您没有这些信息，在继续之前您将需要联系他们。如果您准备好了…',
    'step1Btn' => '现在开始安装',
    // step 2
    'step2Title' => '基础环境检查',
    'step2Desc' => '',
    'step2CheckPhpVersion' => 'PHP 版本要求 8.0.0 或以上',
    'step2CheckHttps' => '站点推荐使用 HTTPS',
    'step2CheckFolderOwnership' => '目录权限',
    'step2CheckPhpExtensions' => 'PHP 扩展检查',
    'step2CheckPhpFunctions' => 'PHP 函数检查',
    'step2CheckStatusSuccess' => '成功',
    'step2CheckStatusFailure' => '失败',
    'step2CheckStatusWarning' => '警告',
    'step2StatusNotEnabled' => '未启用',
    'step2CheckBtn' => '重试',
    'step2Btn' => '确认',
    // step 3
    'step3Title' => '填写数据库信息',
    'step3Desc' => '数据库版本要求 MySQL 8.0.0 或以上',
    'step3DatabaseName' => '数据库名',
    'step3DatabaseNameIntro' => '将 Fresns 安装到的数据库名称。',
    'step3DatabaseUsername' => '用户名',
    'step3DatabaseUsernameIntro' => '您的数据库用户名。',
    'step3DatabasePassword' => '密码',
    'step3DatabasePasswordIntro' => '您的数据库密码。',
    'step3DatabaseHost' => '数据库主机',
    'step3DatabaseHostIntro' => '如果 localhost 不能用，您通常可以从主机提供商处得到正确的信息。',
    'step3DatabaseTablePrefix' => '表前缀',
    'step3DatabaseTablePrefixIntro' => '如果您希望在同一个数据库安装多个 Fresns，请修改前缀。',
    'step3CheckDatabaseFailure' => '建立数据库连接时出错，我们无法连接您的数据库',
    'step3Btn' => '提交',
    // step 4
    'step4Title' => '填写管理信息',
    'step4Desc' => '您需要填写一些基本信息。无需担心填错，这些信息以后可以再次修改。管理员邮箱和手机号可以二选一，也可以全部填写。',
    'step4BackendHost' => '后端地址',
    'step4MemberNickname' => '管理员昵称',
    'step4AccountEmail' => '管理员邮箱',
    'step4AccountPhoneNumber' => '管理员手机号',
    'step4AccountPassword' => '登录密码',
    'step4CheckAccount' => '邮箱和手机号必须填一个',
    'step4Btn' => '安装 Fresns',
    // step 5
    'step5Title' => '成功！',
    'step5Desc' => 'Fresns 安装完成。谢谢！',
    'step5Account' => '登录账号：您填写的邮箱或手机号（带国际区号的手机号）',
    'step5Password' => '登录密码：您填写的密码',
    'step5Btn' => '前往登录',
];
