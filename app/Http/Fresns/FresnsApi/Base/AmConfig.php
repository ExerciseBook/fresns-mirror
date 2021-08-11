<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Base;

class AmConfig
{
    const VIEW_MODE_PUBLIC = 1;
    const VIEW_MODE_PRIVATE = 2;


    //用户状态 users > deleted_at 注销，仅可请求「登录」和「恢复」 [用户基本信息] [退出登录]接口。
    const CHECK_USER_DELETE_URI = [
        '/api/fresns/user/login',
        '/api/fresns/user/restore',
        '/api/fresns/user/detail',
        '/api/fresns/user/logout',
    ];

    //用户状态 users > is_enable 可以请求[用户基本信息] [退出登录]
    const CHECK_USER_IS_ENABLE_URI = [
        '/api/fresns/user/detail',
        '/api/fresns/user/logout',
    ];

    //消息内容类
    const NOTICE_CONTENT_URI = [
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
    ];

    const CONFIGS_LIST = 'configs_list';
    //系统配置api接口返回
    const CONFIGS_LIST_API = 'configs_list_api';

    const HEADER_FIELD_ARR = [
        'platform',
        'version',
        'versionInt',
        'timestamp',
        'appId',
        'sign',
    ];

    const SIGN_FIELD_ARR = [
        'platform',
        'version',
        'versionInt',
        'timestamp',
        'uid',
        'mid',
        'token',
        'appId',
    ];

    //公开模式 public uid 必传
    const PUBLIC_UID_URI_ARR = [
        '/api/fresns/info/downloadFile',
        '/api/fresns/info/summary',
        '/api/fresns/user/logout',
        '/api/fresns/user/delete',
        '/api/fresns/user/restore',
        '/api/fresns/user/detail',
        '/api/fresns/user/edit',
        '/api/fresns/user/walletLogs',
        '/api/fresns/member/auth',
        '/api/fresns/member/edit',
        '/api/fresns/member/mark',
        '/api/fresns/member/delete',
        '/api/fresns/notify/unread',
        '/api/fresns/notify/lists',
        '/api/fresns/notify/read',
        '/api/fresns/notify/delete',
        '/api/fresns/dialog/lists',
        '/api/fresns/dialog/messages',
        '/api/fresns/dialog/read',
        '/api/fresns/dialog/send',
        '/api/fresns/dialog/delete',
        '/api/fresns/post/follows',
        '/api/fresns/editor/lists',
        '/api/fresns/editor/detail',
        '/api/fresns/editor/create',
        '/api/fresns/editor/uploadToken',
        '/api/fresns/editor/upload',
        '/api/fresns/editor/update',
        '/api/fresns/editor/delete',
        '/api/fresns/editor/publish',
        '/api/fresns/editor/submit',
        '/api/fresns/editor/revoke',
    ];

    //公开模式mid必传
    const PUBLIC_MID_URI_ARR = [
        '/api/fresns/info/downloadFile',
        '/api/fresns/info/summary',
        '/api/fresns/member/edit',
        '/api/fresns/member/mark',
        '/api/fresns/member/delete',
        '/api/fresns/notify/unread',
        '/api/fresns/notify/lists',
        '/api/fresns/notify/delete',
        '/api/fresns/dialog/lists',
        '/api/fresns/dialog/messages',
        '/api/fresns/dialog/read',
        '/api/fresns/dialog/send',
        '/api/fresns/dialog/delete',
        '/api/fresns/post/follows',
        '/api/fresns/editor/lists',
        '/api/fresns/editor/detail',
        '/api/fresns/editor/create',
        '/api/fresns/editor/uploadToken',
        '/api/fresns/editor/upload',
        '/api/fresns/editor/update',
        '/api/fresns/editor/delete',
        '/api/fresns/editor/publish',
        '/api/fresns/editor/submit',
        '/api/fresns/editor/revoke',
    ];

    //公开模式public token必传
    const PUBLIC_TOKEN_URI_ARR = [
        '/api/fresns/info/downloadFile',
        '/api/fresns/info/summary',
        '/api/fresns/user/logout',
        '/api/fresns/user/delete',
        '/api/fresns/user/restore',
        '/api/fresns/user/detail',
        '/api/fresns/user/edit',
        '/api/fresns/user/walletLogs',
        '/api/fresns/member/auth',
        '/api/fresns/member/edit',
        '/api/fresns/member/mark',
        '/api/fresns/member/delete',
        '/api/fresns/notify/unread',
        '/api/fresns/notify/lists',
        '/api/fresns/notify/read',
        '/api/fresns/notify/delete',
        '/api/fresns/dialog/lists',
        '/api/fresns/dialog/messages',
        '/api/fresns/dialog/read',
        '/api/fresns/dialog/send',
        '/api/fresns/dialog/delete',
        '/api/fresns/post/follows',
        '/api/fresns/editor/lists',
        '/api/fresns/editor/detail',
        '/api/fresns/editor/create',
        '/api/fresns/editor/uploadToken',
        '/api/fresns/editor/upload',
        '/api/fresns/editor/update',
        '/api/fresns/editor/delete',
        '/api/fresns/editor/publish',
        '/api/fresns/editor/submit',
        '/api/fresns/editor/revoke',

    ];

    //公有模式public deviceInfo必传
    const PUBLIC_DEVICEINFO_URI_ARR = [
        '/api/fresns/info/uploadLog',
        '/api/fresns/info/downloadFile',
        '/api/fresns/user/register',
        '/api/fresns/user/login',
        '/api/fresns/user/delete',
        '/api/fresns/user/restore',
        '/api/fresns/user/reset',
        '/api/fresns/user/edit',
        '/api/fresns/member/auth',
        '/api/fresns/member/edit',
        '/api/fresns/editor/create',
        '/api/fresns/editor/publish',
        '/api/fresns/editor/submit',

    ];

    //私有模式private uid必传
    const PRIVATE_UID_URI_ARR = [
        '/api/fresns/info/extensions',
        '/api/fresns/info/summary',
        '/api/fresns/info/emojis',
        '/api/fresns/info/stopWords',
        '/api/fresns/info/inputtips',
        '/api/fresns/info/downloadFile',
        '/api/fresns/user/logout',
        '/api/fresns/user/delete',
        '/api/fresns/user/restore',
        '/api/fresns/user/detail',
        '/api/fresns/user/edit',
        '/api/fresns/user/walletLogs',
        '/api/fresns/member/auth',
        '/api/fresns/member/edit',
        '/api/fresns/member/mark',
        '/api/fresns/member/delete',
        '/api/fresns/member/detail',
        '/api/fresns/member/lists',
        '/api/fresns/member/interactions',
        '/api/fresns/member/markLists',
        '/api/fresns/member/roles',
        '/api/fresns/notify/unread',
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
        '/api/fresns/editor/create',
        '/api/fresns/editor/uploadToken',
        '/api/fresns/editor/upload',
        '/api/fresns/editor/update',
        '/api/fresns/editor/delete',
        '/api/fresns/editor/publish',
        '/api/fresns/editor/submit',
        '/api/fresns/editor/revoke',
    ];

    //私有模式 private mid 必传
    const PRIVATE_MID_URI_ARR = [
        '/api/fresns/info/extensions',
        '/api/fresns/info/summary',
        '/api/fresns/info/emojis',
        '/api/fresns/info/stopWords',
        '/api/fresns/info/inputtips',
        '/api/fresns/info/downloadFile',
        '/api/fresns/member/edit',
        '/api/fresns/member/mark',
        '/api/fresns/member/delete',
        '/api/fresns/member/detail',
        '/api/fresns/member/lists',
        '/api/fresns/member/interactions',
        '/api/fresns/member/markLists',
        '/api/fresns/member/roles',
        '/api/fresns/notify/unread',
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
        '/api/fresns/editor/create',
        '/api/fresns/editor/uploadToken',
        '/api/fresns/editor/upload',
        '/api/fresns/editor/update',
        '/api/fresns/editor/delete',
        '/api/fresns/editor/publish',
        '/api/fresns/editor/submit',
        '/api/fresns/editor/revoke',
    ];

    //私有模式 private token 必传
    const PRIVATE_TOKEN_URI_ARR = [
        '/api/fresns/info/summary',
        '/api/fresns/info/extensions',
        '/api/fresns/info/emojis',
        '/api/fresns/info/stopWords',
        '/api/fresns/info/inputtips',
        '/api/fresns/info/downloadFile',
        '/api/fresns/user/logout',
        '/api/fresns/user/delete',
        '/api/fresns/user/restore',
        '/api/fresns/user/detail',
        '/api/fresns/user/edit',
        '/api/fresns/user/walletLogs',
        '/api/fresns/member/auth',
        '/api/fresns/member/edit',
        '/api/fresns/member/mark',
        '/api/fresns/member/delete',
        '/api/fresns/member/detail',
        '/api/fresns/member/lists',
        '/api/fresns/member/interactions',
        '/api/fresns/member/markLists',
        '/api/fresns/member/roles',
        '/api/fresns/notify/unread',
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
        '/api/fresns/editor/create',
        '/api/fresns/editor/uploadToken',
        '/api/fresns/editor/upload',
        '/api/fresns/editor/update',
        '/api/fresns/editor/delete',
        '/api/fresns/editor/publish',
        '/api/fresns/editor/submit',
        '/api/fresns/editor/revoke',
    ];

    //私有模式 private deviceInfo 必传
    const PRIVATE_DEVICEINFO_URI_ARR = [
        '/api/fresns/info/uploadLog',
        '/api/fresns/info/downloadFile',
        '/api/fresns/user/login',
        '/api/fresns/user/delete',
        '/api/fresns/user/restore',
        '/api/fresns/user/reset',
        '/api/fresns/user/edit',
        '/api/fresns/member/auth',
        '/api/fresns/member/edit',
        '/api/fresns/editor/create',
        '/api/fresns/editor/publish',
        '/api/fresns/editor/submit',


    ];
}
