<?php
/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Center\AmGlobal;

// 模型配置
use App\Base\Config\BaseConfig;

class AmConfig extends BaseConfig
{
    const CONFIGS_LIST = 'configs_list';
    //系统配置api接口返回
    const CONFIGS_LIST_API = 'configs_list_api';

    //不需要新增的

    const URI_CONVERSION_OBJECT_TYPE = [
        '未知' => [
            '/api/fresns/info/configs',
            '/api/fresns/info/extensions',
            '/api/fresns/info/summary',
            '/api/fresns/info/emojis',
            '/api/fresns/info/stopWords',
            '/api/fresns/info/sendVerifyCode',
            '/api/fresns/info/inputtips',
            '/api/fresns/info/uploadLog',
            '/api/fresns/info/downloadFile',
            '/api/fresns/user/logout',
            '/api/fresns/user/restore',
            '/api/fresns/user/detail',
            '/api/fresns/user/walletLogs',
            '/api/fresns/member/mark',
            '/api/fresns/member/delete',
            '/api/fresns/member/detail',
            '/api/fresns/member/lists',
            '/api/fresns/member/interactions',
            '/api/fresns/member/markLists',
            '/api/fresns/notify/lists',
            '/api/fresns/notify/read',
            '/api/fresns/notify/delete',
            '/api/fresns/dialog/lists',
            '/api/fresns/dialog/messages',
            '/api/fresns/dialog/read',
            '/api/fresns/dialog/send',
            '/api/fresns/dialog/delete',
            '/api/fresns/group/trees',
            '/api/fresns/group/lists',
            '/api/fresns/group/detail',
            '/api/fresns/hashtag/lists',
            '/api/fresns/hashtag/detail',
            '/api/fresns/post/lists',
            '/api/fresns/post/detail',
            '/api/fresns/post/follows',
            '/api/fresns/post/nearbys',
            '/api/fresns/comment/lists',
            '/api/fresns/comment/detail',
            '/api/fresns/editor/lists',
            '/api/fresns/editor/detail',
            // '/api/fresns/editor/create',
            '/api/fresns/editor/uploadToken',
            '/api/fresns/editor/upload',
            '/api/fresns/editor/update',
            '/api/fresns/editor/delete',
            // '/api/fresns/editor/publish',
            // '/api/fresns/editor/submit',
            '/api/fresns/editor/revoke',

        ],
        '注册' => [
            '/api/fresns/user/register',
        ],
        '登录' => [
            '/api/fresns/user/login',
        ],
        '注销' => [
            '/api/fresns/user/delete',
        ],
        '重置用户密码' => [
            '/api/fresns/user/reset',
        ],
        '修改用户资料' => [
            '/api/fresns/user/edit',
        ],
        '成员登录' => [
            '/api/fresns/member/auth',
        ],
        '修改成员资料' => [
            '/api/fresns/member/edit',
        ],
    ];

    //链接转换为接口名称
    const URI_API_NAME_MAP = [
        '/api/fresns/info/configs' => '系统配置信息',
        '/api/fresns/info/extensions' => '扩展配置信息',
        '/api/fresns/info/summary' => '全局摘要信息',
        '/api/fresns/info/emojis' => '表情包',
        '/api/fresns/info/stopWords' => '处理词列表',
        '/api/fresns/info/sendVerifyCode' => '发送验证码',
        '/api/fresns/info/inputtips' => '输入提示查询',
        '/api/fresns/info/uploadLog' => '上传交互日志',
        '/api/fresns/info/downloadFile' => '下载内容文件',
        '/api/fresns/user/register' => '注册',
        '/api/fresns/user/login' => '登录',
        '/api/fresns/user/logout' => '退出登录',
        '/api/fresns/user/delete' => '注销',
        '/api/fresns/user/restore' => '恢复',
        '/api/fresns/user/reset' => '重置密码',
        '/api/fresns/user/detail' => '用户基本信息',
        '/api/fresns/user/edit' => '修改用户资料',
        '/api/fresns/user/walletLogs' => '钱包交易记录',
        '/api/fresns/member/auth' => '成员登录',
        '/api/fresns/member/edit' => '修改成员资料',
        '/api/fresns/member/mark' => '操作标记内容',
        '/api/fresns/member/delete' => '操作删除内容',
        '/api/fresns/member/markLists' => '获取标记内容[列表]',
        '/api/fresns/member/detail' => '获取成员[单个]',
        '/api/fresns/member/lists' => '获取成员[列表]',
        '/api/fresns/member/interactions' => '获取成员[互动列表]',
        '/api/fresns/notify/lists' => '[通知]获取消息列表',
        '/api/fresns/notify/read' => '[通知]更新阅读状态',
        '/api/fresns/notify/delete' => '[通知]删除消息',
        '/api/fresns/dialog/lists' => '[会话]获取会话列表',
        '/api/fresns/dialog/messages' => '[会话]获取消息列表',
        '/api/fresns/dialog/read' => '[会话]更新阅读状态',
        '/api/fresns/dialog/send' => '[会话]发送消息',
        '/api/fresns/dialog/delete' => '[会话]删除消息',
        '/api/fresns/group/trees' => '获取小组[树结构列表]',
        '/api/fresns/group/lists' => '获取小组[列表]',
        '/api/fresns/group/detail' => '获取小组[单条]',
        '/api/fresns/hashtag/lists' => '获取话题[列表]',
        '/api/fresns/hashtag/detail' => '获取话题[单条]',
        '/api/fresns/post/lists' => '获取帖子[列表]',
        '/api/fresns/post/detail' => '获取帖子[单条]',
        '/api/fresns/post/follows' => '获取帖子关注的[列表]',
        '/api/fresns/post/nearbys' => '获取帖子附近的[列表]',
        '/api/fresns/comment/lists' => '获取评论[列表]',
        '/api/fresns/comment/detail' => '获取评论[单条]',
        '/api/fresns/editor/lists' => '获取草稿列表',
        '/api/fresns/editor/detail' => '获取草稿详情',
        '/api/fresns/editor/create' => '创建新草稿',
        '/api/fresns/editor/uploadToken' => '获取上传凭证',
        '/api/fresns/editor/upload' => '上传文件',
        '/api/fresns/editor/update' => '更新草稿内容',
        '/api/fresns/editor/delete' => '删除草稿或附属文件',
        '/api/fresns/editor/publish' => '快速发表',
        '/api/fresns/editor/submit' => '提交内容正式发表',
        '/api/fresns/editor/revoke' => '撤回审核中草稿',
    ];

    //链接转换 //未知
    const URI_CONVERSION_OBJECT_NAME = [
        'App\Http\FresnsDb\FresnsConfigs' => [
            '/api/fresns/info/configs',
        ],
        'App\Http\FresnsDb\FresnsPluginUsages' => [
            '/api/fresns/info/configs',
        ],
        'App\Http\FresnsDb\FresnsEmojis' => [
            '/api/fresns/info/emojis',
        ],
        'App\Http\FresnsDb\FresnsStopWords' => [
            '/api/fresns/info/stopWords',
        ],
        'App\Http\FresnsDb\FresnsVerifyCodes' => [
            '/api/fresns/info/sendVerifyCode',
        ],
        'App\Http\FresnsDb\FresnsSessionLogs' => [
            '/api/fresns/info/uploadLog',
        ],
        'App\Http\FresnsDb\FresnsFiles' => [
            '/api/fresns/info/downloadFile',
            '/api/fresns/editor/uploadToken',
            '/api/fresns/editor/upload',

        ],
        'App\Http\FresnsDb\FresnsUsers' => [
            '/api/fresns/user/register',
            '/api/fresns/user/login',
            '/api/fresns/user/logout',
            '/api/fresns/user/delete',
            '/api/fresns/user/restore',
            '/api/fresns/user/restore',
            '/api/fresns/user/reset',
            '/api/fresns/user/detail',
            '/api/fresns/user/edit',
        ],
        'App\Http\FresnsDb\FresnsWalletLogs' => [
            '/api/fresns/user/walletLogs',
        ],
        'App\Http\FresnsDb\FresnsMembers' => [
            '/api/fresns/member/auth',
            '/api/fresns/member/edit',
            '/api/fresns/member/mark',
            '/api/fresns/member/delete',
            '/api/fresns/member/detail',
            '/api/fresns/member/lists',
            '/api/fresns/member/interactions',
            '/api/fresns/member/markLists',
        ],
        'App\Http\FresnsDb\FresnsNotifies' => [
            '/api/fresns/notify/lists',
            '/api/fresns/notify/read',
            '/api/fresns/notify/delete',
        ],
        'App\Http\FresnsDb\FresnsDialogs' => [
            '/api/fresns/dialog/lists',
            '/api/fresns/dialog/messages',
            '/api/fresns/dialog/read',
            // '/api/fresns/dialog/send',
            '/api/fresns/dialog/delete',
        ],
        'App\Http\FresnsDb\FresnsDialogMessages' => [
            '/api/fresns/dialog/send',
        ],
        'App\Http\FresnsDb\FresnsGroups' => [
            '/api/fresns/group/trees',
            '/api/fresns/group/lists',
            '/api/fresns/group/detail',
        ],
        'App\Http\FresnsDb\FresnsHashtags' => [
            '/api/fresns/hashtag/lists',
            '/api/fresns/hashtag/detail',
        ],
        'App\Http\FresnsDb\FresnsPosts' => [
            '/api/fresns/post/lists',
            '/api/fresns/post/detail',
            '/api/fresns/post/follows',
            '/api/fresns/post/nearbys',
        ],
        'App\Http\FresnsDb\FresnsComments' => [
            '/api/fresns/comment/lists',
            '/api/fresns/comment/detail',
        ],
    ];
}
