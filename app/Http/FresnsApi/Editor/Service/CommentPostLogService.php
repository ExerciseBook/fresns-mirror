<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Editor\Service;

use App\Http\Center\Helper\PluginHelper;
use App\Http\Center\Scene\FileSceneService;
use App\Http\FresnsApi\Helpers\ApiCommonHelper;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\FresnsDb\FresnsCommentAppends\FresnsCommentAppends;
use App\Http\FresnsDb\FresnsCommentLogs\FresnsCommentLogs;
use App\Http\FresnsDb\FresnsComments\FresnsComments;
use App\Http\FresnsDb\FresnsExtendLinkeds\FresnsExtendLinkedsConfig;
use App\Http\FresnsDb\FresnsExtends\FresnsExtends;
use App\Http\FresnsDb\FresnsFileAppends\FresnsFileAppends;
use App\Http\FresnsDb\FresnsFiles\FresnsFiles;
use App\Http\FresnsDb\FresnsGroups\FresnsGroups;
use App\Http\FresnsDb\FresnsMemberRoles\FresnsMemberRoles;
use App\Http\FresnsDb\FresnsMembers\FresnsMembers;
use App\Http\FresnsDb\FresnsPostAllows\FresnsPostAllowsConfig;
use App\Http\FresnsDb\FresnsPostAppends\FresnsPostAppends;
use App\Http\FresnsDb\FresnsPostLogs\FresnsPostLogs;
use App\Http\FresnsDb\FresnsPosts\FresnsPosts;
use App\Http\FresnsDb\FresnsPosts\FresnsPostsConfig;
use App\Http\FresnsDb\FresnsStopWords\FresnsStopWords;
use App\Http\Center\AmGlobal\GlobalService;
use App\Http\Center\Common\LogService;
use Illuminate\Support\Facades\DB;

class CommentPostLogService
{
    // 获取该帖子现有内容创建草稿。
    public static function postLogInsert($uuid, $mid)
    {
        $postInfo = FresnsPosts::where('uuid', $uuid)->first();
        $postAppend = FresnsPostAppends::findAppend('post_id', $postInfo['id']);
        // 编辑器配置
        $is_plugin_edit = $postAppend['is_plugin_edit'];
        $plugin_unikey = $postAppend['plugin_unikey'];
        // $editor_json = [];
        // $editor_json['pluginEdit'] = $postAppend['is_plugin_edit'] == 0 ? "false" : "true";
        // $editor_json['pluginUnikey'] = $postAppend['plugin_unikey'];
        // $editStatus = [];
        // $editStatus['isUse'] = true;
        // $editStatus['isDelete'] = 2;
        // $editor_json['editStatus'] = $editStatus;

        // 特定成员配置
        $member_list_json = [];
        if ($postAppend['member_list_status'] == 1) {
            $member_list_json['memberListStatus'] = $postAppend['member_list_status'];
            $member_list_json['pluginUnikey'] = $postAppend['member_list_plugin_unikey'];
            // member_list_name多语言
            $memberListName = ApiLanguageHelper::getAllLanguages(FresnsPostsConfig::CFG_TABLE, 'member_list_name',
                $postInfo['id']);
            if ($memberListName) {
                $memberListName1 = [];
                foreach ($memberListName as $m) {
                    $memberNameArr = [];
                    // $editStatus = [];
                    $memberNameArr['langTag'] = $m['lang_tag'];
                    $memberNameArr['name'] = $m['lang_content'];
                    $memberListName1[] = $memberNameArr;
                }
            }
            $member_list_json['memberListName'] = $memberListName1;
        }
        // 评论设置
        $comment_set_json = [];
        if ($postAppend['comment_btn_status'] == 1) {
            $comment_set_json['btnStatus'] = $postAppend['comment_btn_status'];
            $comment_set_json['pluginUnikey'] = $postAppend['comment_btn_plugin_unikey'];
            // btnName（多语言）
            $btnName = ApiLanguageHelper::getAllLanguages(FresnsPostsConfig::CFG_TABLE, 'comment_btn_name',
                $postInfo['id']);
            // dump($btnName);
            if ($btnName) {
                $btnName1 = [];
                foreach ($btnName as $f) {
                    $btnNameArr = [];
                    // $editStatus = [];
                    $btnNameArr['langTag'] = $f['lang_tag'];
                    $btnNameArr['name'] = $f['lang_content'];
                    // $editStatus['isUse'] = true;
                    // $editStatus['isDelete'] = 2;
                    // $btnNameArr['editStatus'] = $editStatus ;
                    $btnName1[] = $btnNameArr;
                }
            }
            $comment_set_json['btnName'] = $btnName1;
            // dd($comment_set_json);
            // $editStatus = [];
            // $editStatus['isUse'] = true;
            // $editStatus['isDelete'] = 2;
            // $comment_set_json['editStatus'] = $editStatus;
        }

        // 阅读权限配置
        $allow_json = [];
        if ($postInfo['is_allow'] == 1) {
            $allow_json['isAllow'] = $postInfo['is_allow'];
            $allow_json['pluginUnikey'] = $postAppend['allow_plugin_unikey'];
            // btnName（多语言）
            $btnName = ApiLanguageHelper::getAllLanguages(FresnsPostsConfig::CFG_TABLE, 'allow_btn_name',
                $postInfo['id']);
            if ($btnName) {
                $btnName1 = [];
                foreach ($btnName as $f) {
                    $btnNameArr = [];
                    $editStatus = [];
                    $btnNameArr['langTag'] = $f['lang_tag'];
                    $btnNameArr['name'] = $f['lang_content'];
                    // $editStatus['isUse'] = false;
                    // $editStatus['isDelete'] = 2;
                    // $btnNameArr['editStatus'] = $editStatus ;
                    $btnName1[] = $btnNameArr;
                }
            }
            $allow_json['btnName'] = $btnName1;
            $allow_json['proportion'] = $postAppend['allow_proportion'];
            $allow_json['permission'] = [];
            // $memberInfo = TweetMembers::find($mid);
            $allowMemberInfo = DB::table(FresnsPostAllowsConfig::CFG_TABLE)->where('type', 1)->where('post_id',
                $postInfo['id'])->pluck('object_id')->toArray();
            // dd($allowMemberInfo);
            $result = [];
            if ($allowMemberInfo) {
                $memberInfo = FresnsMembers::whereIn('id', $allowMemberInfo)->get();
                foreach ($memberInfo as $m) {
                    $arr = [];
                    $arr['mid'] = $m['uuid'];
                    $arr['membername'] = $m['name'];
                    $arr['nickname'] = $m['nickname'];
                    // $editStatus = [];
                    // $editStatus['isUse'] = false;
                    // $editStatus['isDelete'] = 2;
                    // $arr['editStatus'] = $editStatus;
                    $result[] = $arr;
                }
            }
            $allow_json['permission']['members'] = $result;

            // 成员角色关联表表
            $roleRels = DB::table(FresnsPostAllowsConfig::CFG_TABLE)->where('type', 2)->where('post_id',
                $postInfo['id'])->pluck('object_id')->toArray();
            // 成员角色表
            $result = [];
            if ($roleRels) {
                $memberRole = FresnsMemberRoles::whereIn('id', $roleRels)->get();
                foreach ($memberRole as $m) {
                    $arr = [];
                    $arr['rid'] = $m['id'];
                    $arr['name'] = $m['name'];
                    // $editStatus = [];
                    // $editStatus['isUse'] = false;
                    // $editStatus['isDelete'] = 2;
                    // $arr['editStatus'] = $editStatus;
                    $result[] = $arr;
                }
            }
            $allow_json['permission']['roles'] = $result;
            // $editStatus = [];
            // $editStatus['isUse'] = true;
            // $editStatus['isDelete'] = 2;
            // $allow_json['editStatus'] = $editStatus;
        }
        // 	位置信息
        $location_json = [];
        $location_json['isLbs'] = $postInfo['is_lbs'] ?? '';
        $location_json['mapId'] = $postInfo['map_id'] ?? '';
        $location_json['latitude'] = $postInfo['map_latitude'] ?? '';
        $location_json['longitude'] = $postInfo['map_longitude'] ?? '';
        $location_json['scale'] = $postAppend['map_scale'] ?? '';
        $location_json['poi'] = $postAppend['map_poi'] ?? '';
        $location_json['poiId'] = $postAppend['map_poi_id'] ?? '';
        $location_json['nation'] = $postAppend['map_nation'] ?? '';
        $location_json['province'] = $postAppend['map_province'] ?? '';
        $location_json['city'] = $postAppend['map_city'] ?? '';
        $location_json['district'] = $postAppend['map_district'] ?? '';
        $location_json['adcode'] = $postAppend['map_adcode'] ?? '';
        $location_json['address'] = $postAppend['map_address'] ?? '';
        // $editStatus = [];
        // $editStatus['isUse'] = true;
        // $editStatus['isDelete'] = 2;
        // $location_json['editStatus'] = $editStatus;

        // 	附属文件
        // $files = ApiFileHelper::getFileInfoByTable(FresnsCommentsConfig::CFG_TABLE,$postInfo['id']);
        $more_json = json_decode($postInfo['more_json'], true);
        $files = null;
        if (isset($more_json['files'])) {
            $files = $more_json['files'];
        }
        // if($files){
        //     $editStatus = [];
        //     $editStatus['isUse'] = true;
        //     $editStatus['isDelete'] = 2;
        //     $files['editStatus'] = $editStatus;
        // }

        // 扩展内容
        $extends_json = [];
        $result = [];
        $extendLink = DB::table(FresnsExtendLinkedsConfig::CFG_TABLE)->where('linked_type', 1)->where('linked_id',
            $postInfo['id'])->get()->toArray();
        if ($extendLink) {
            // $extend = FresnsExtends::whereIn('id',$extendLink)->get();
            $arr = [];
            foreach ($extendLink as $e) {
                $extend = FresnsExtends::where('id', $e->extend_id)->first();
                if ($extend) {
                    $arr['eid'] = $extend['uuid'];
                    $arr['canDelete'] = $extend['post_id'] ? 'false' : 'true';
                    $arr['rankNum'] = $e->rank_num ?? 9;
                }
                $result[] = $arr;
            }
        }

        $extends_json = $result;
        // dd($extends_json);
        if (! empty($member_list_json)) {
            $member_list_json = json_encode($member_list_json);
        } else {
            $member_list_json = null;
        }
        if (! empty($comment_set_json)) {
            $comment_set_json = json_encode($comment_set_json);
        } else {
            $comment_set_json = null;
        }
        if (! empty($allow_json)) {
            $allow_json = json_encode($allow_json);
        } else {
            $allow_json = null;
        }
        if (! empty($location_json)) {
            $location_json = json_encode($location_json);
        } else {
            $location_json = null;
        }
        if (! empty($files)) {
            $files = json_encode($files);
        } else {
            $files = null;
        }
        if (! empty($extends_json)) {
            $extends_json = json_encode($extends_json);
        } else {
            $extends_json = null;
        }
        $postInput = [
            'member_id' => $mid,
            'post_id' => $postInfo['id'],
            'platform_id' => $postAppend['platform_id'],
            'group_id' => $postInfo['group_id'],
            'type' => $postInfo['type'],
            'title' => $postInfo['title'],
            'content' => $postAppend['content'],
            'is_markdown' => $postAppend['is_markdown'],
            'is_anonymous' => $postInfo['is_anonymous'],
            'is_plugin_edit' => $is_plugin_edit,
            'plugin_unikey' => $plugin_unikey,
            // 'editor_json' => json_encode($editor_json),
            'member_list_json' => $member_list_json,
            'comment_set_json' => $comment_set_json,
            'allow_json' => $allow_json,
            'location_json' => $location_json,
            'files_json' => $files,
            'extends_json' => $extends_json,
        ];
        // dd($postInput);
        $FresnsPostLogsService = new FresnsPostLogs();
        $postLogId = $FresnsPostLogsService->store($postInput);

        return $postLogId;
    }

    // 获取该评论现有内容创建草稿
    public static function commentLogInsert($uuid, $mid)
    {
        $commentInfo = FresnsComments::where('uuid', $uuid)->first();
        $commentAppend = FresnsCommentAppends::findAppend('comment_id', $commentInfo['id']);

        // 评论的帖子信息
        $postInfo = FresnsPosts::find($commentInfo['post_id']);
        // 编辑器配置
        $is_plugin_edit = $commentAppend['is_plugin_edit'];
        $plugin_unikey = $commentAppend['plugin_unikey'];
        // $editor_json = [];
        // $editor_json['pluginEdit'] = $commentAppend['is_plugin_edit'] == 0 ? "false" : "true";
        // $editor_json['pluginUnikey'] = $commentAppend['plugin_unikey'];
        // $editStatus = [];
        // $editStatus['isUse'] = false;
        // $editStatus['isDelete'] = 2;
        // $editor_json['editStatus'] = $editStatus;

        // 位置信息
        $location_json = [];
        $location_json['isLbs'] = $commentInfo['is_lbs'];
        $location_json['mapId'] = $commentAppend['map_id'];
        $location_json['latitude'] = $commentAppend['map_latitude'];
        $location_json['longitude'] = $commentAppend['map_longitude'];
        $location_json['scale'] = $commentAppend['map_scale'];
        $location_json['poi'] = $commentAppend['map_poi'];
        $location_json['poiId'] = $commentAppend['map_poi_id'];
        $location_json['nation'] = $commentAppend['map_nation'];
        $location_json['province'] = $commentAppend['map_province'];
        $location_json['city'] = $commentAppend['map_city'];
        $location_json['district'] = $commentAppend['map_district'];
        $location_json['adcode'] = $commentAppend['map_adcode'];
        $location_json['address'] = $commentAppend['map_address'];
        // $editStatus = [];
        // $editStatus['isUse'] = false;
        // $editStatus['isDelete'] = 2;
        // $location_json['editStatus'] = $editStatus;

        // 	附属文件
        // $files = ApiFileHelper::getFileInfoByTable(FresnsCommentsConfig::CFG_TABLE,$commentInfo['id']);
        $more_json = json_decode($commentInfo['more_json'], true);
        $files = $more_json['files'];
        // if($files){
        //     $editStatus = [];
        //     $editStatus['isUse'] = false;
        //     $editStatus['isDelete'] = 2;
        //     $files['editStatus'] = $editStatus;
        // }

        // 扩展内容
        // 扩展内容
        $extends_json = [];
        $result = [];
        $extendLink = DB::table(FresnsExtendLinkedsConfig::CFG_TABLE)->where('linked_type', 2)->where('linked_id',
            $commentInfo['id'])->get()->toArray();
        if ($extendLink) {
            // $extend = FresnsExtends::whereIn('id',$extendLink)->get();
            $arr = [];
            foreach ($extendLink as $e) {
                $extend = FresnsExtends::where('id', $e->extend_id)->first();
                if ($extend) {
                    $arr['eid'] = $extend['uuid'];
                    $arr['canDelete'] = $extend['post_id'] ? 'false' : 'true';
                    $arr['rankNum'] = $e->rank_num ?? 9;
                }
                $result[] = $arr;
            }
        }

        $extends_json = $result;
        if (! empty($location_json)) {
            $location_json = json_encode($location_json);
        } else {
            $location_json = null;
        }
        if (! empty($files)) {
            $files = json_encode($files);
        } else {
            $files = null;
        }
        if (! empty($extends_json)) {
            $extends_json = json_encode($extends_json);
        } else {
            $extends_json = null;
        }
        $commentInput = [
            'member_id' => $mid,
            'comment_id' => $commentInfo['id'],
            'post_id' => $commentInfo['post_id'],
            'platform_id' => $commentAppend['platform_id'],
            'type' => $commentInfo['type'],
            'content' => $commentAppend['content'],
            'is_markdown' => $commentAppend['is_markdown'],
            'is_anonymous' => $commentInfo['is_anonymous'],
            // 'editor_json' => json_encode($editor_json),
            'is_plugin_edit' => $is_plugin_edit,
            'plugin_unikey' => $plugin_unikey,
            'location_json' => $location_json,
            'files_json' => $files,
            'extends_json' => $extends_json,
        ];
        $FresnsCommentLogsService = new FresnsCommentLogs();
        $commentLogId = $FresnsCommentLogsService->store($commentInput);

        return $commentLogId;
    }

    // 更新帖子草稿
    public static function updatePostLog($mid)
    {
        $request = request();
        $mid = $mid;
        $logId = $request->input('logId');
        $type = $request->input('type', 'text') ?? 'text';
        $gid = $request->input('gid');
        $gid = FresnsGroups::where('uuid', $gid)->first();
        $title = $request->input('title');
        $content = $request->input('content');
        $isMarkdown = $request->input('isMarkdown', 0);
        $isAnonymous = $request->input('isAnonymous', 0);
        $is_plugin_edit = $request->input('isPluginEdit', 0);
        $plugin_unikey = $request->input('pluginUnikey');
        // $editorJson = $request->input('editorJson');
        $commentSetJson = $request->input('commentSetJson') ?? null;
        $memberListJson = $request->input('memberListJson') ?? null;
        $allowJson = $request->input('allowJson') ?? null;
        $locationJson = $request->input('locationJson') ?? null;
        $filesJson = $request->input('filesJson') ?? null;
        // $extendsJson = $request->input('extendsJson') ?? null;
        $extends_json = json_decode($request->input('extendsJson'), true);
        $extends = [];
        if ($extends_json) {
            $arr = [];
            foreach ($extends_json as $v) {
                $arr['eid'] = $v['eid'];
                $arr['rankNum'] = $v['rankNum'] ?? 9;
                $arr['canDelete'] = $v['canDelete'] ?? true;
                $extends[] = $arr;
            }
        }
        $extendsJson = json_encode($extends);
        $content = self::stopWords($content);
        // dd($type);
        $input = [
            'group_id' => $gid,
            'type' => $type,
            'title' => $title,
            'group_id' => $gid['id'] ?? null,
            'content' => trim($content),
            'is_markdown' => $isMarkdown,
            'is_anonymous' => $isAnonymous,
            // 'editor_json' => $editorJson,
            'is_plugin_edit' => $is_plugin_edit,
            'plugin_unikey' => $plugin_unikey,
            'comment_set_json' => $commentSetJson,
            'member_list_json' => $memberListJson,
            'allow_json' => $allowJson,
            'location_json' => $locationJson,
            'files_json' => $filesJson,
            'extends_json' => $extendsJson,
        ];
        // dd($input);
        FresnsPostLogs::where('id', $logId)->update($input);

        return true;
    }

    // 更新评论草稿
    public static function updateCommentLog($mid)
    {
        $request = request();
        $mid = $mid;
        $logId = $request->input('logId');
        $type = $request->input('type', 'text') ?? 'text';
        $content = $request->input('content');
        $isMarkdown = $request->input('isMarkdown', 0);
        $isAnonymous = $request->input('isAnonymous', 0);
        // $editorJson = $request->input('editorJson');
        $is_plugin_edit = $request->input('isPluginEdit', 0);
        $plugin_unikey = $request->input('pluginUnikey');
        $locationJson = $request->input('locationJson');
        $filesJson = $request->input('filesJson');
        // $extendsJson = $request->input('extendsJson');
        $extends_json = json_decode($request->input('extendsJson'), true);
        $content = self::stopWords($content);
        $extends = [];
        if ($extends_json) {
            $arr = [];
            foreach ($extends_json as $v) {
                $arr['eid'] = $v['eid'];
                $arr['rankNum'] = $v['rankNum'] ?? 9;
                $arr['canDelete'] = $v['canDelete'] ?? true;
                $extends[] = $arr;
            }
        }
        $extendsJson = json_encode($extends);
        $input = [
            'type' => $type,
            'content' => trim($content),
            'is_markdown' => $isMarkdown == 'false' ? 0 : 1,
            'is_anonymous' => $isAnonymous == 'false' ? 0 : 1,
            // 'editor_json' => $editorJson,
            'is_plugin_edit' => $is_plugin_edit,
            'plugin_unikey' => $plugin_unikey,
            'location_json' => $locationJson,
            'files_json' => $filesJson,
            'extends_json' => $extendsJson,
        ];
        // dd($input);
        FresnsCommentLogs::where('id', $logId)->update($input);

        return true;
    }

    // 快速发表创建草稿(post)
    public static function publishCreatedPost($request)
    {
        $member_id = GlobalService::getGlobalKey('member_id');
        $content = $request->input('content');
        $postGid = $request->input('postGid');
        $postTitle = $request->input('postTitle');
        $isMarkdown = $request->input('isMarkdown');
        // $isAnonymous = $request->input('isAnonymous',0);
        $file = request()->file('file');

        $fileInfo = $request->input('fileInfo');

        $eid = $request->input('eid');
        $extends = [];
        // 扩展信息
        $pluginTypeArr = [];
        if ($eid) {
            $eid = json_decode($eid, true);
            foreach ($eid as $e) {
                $arr = [];
                $extendsInfo = FresnsExtends::where('uuid', $e)->first();
                if ($extendsInfo) {
                    $arr['eid'] = $e;
                    $arr['canDelete'] = $extendsInfo['post_id'] ? 'false' : 'true';
                    $arr['rankNum'] = $extendsInfo['rank_num'] ?? 9;
                    $pluginTypeArr[] = $extendsInfo['plugin_unikey'];
                    $extends[] = $arr;
                }
            }
        }
        $imageType = [];
        $idArr = [];
        if ($file) {
            $idArr = self::publishUpload(1);
            $imageType = ['image'];
        }
        if ($fileInfo) {
            $idArr = self::publishUploadFileInfo($fileInfo);
            $imageType = self::getFileType($fileInfo);
        }
        $fileArr = [];

        if (! empty($idArr)) {
            $fileArr = self::getFilesByIdArr($idArr);
        }
        $typeArr = array_unique(array_merge($pluginTypeArr, $imageType));
        if (empty($typeArr)) {
            $type = 'text';
        } else {
            $type = implode(',', $typeArr);
        }
        // 查询group_id
        $group_id = null;
        if ($postGid) {
            $group = FresnsGroups::where('uuid', $postGid)->first();
            $group_id = $group['id'] ?? null;
        }
        $content = self::stopWords($content);

        $input = [
            'group_id' => $group_id,
            'platform_id' => $request->header('platform'),
            'member_id' => $member_id,
            'title' => $postTitle,
            'content' => strip_tags(trim($content)),
            'is_markdown' => $isMarkdown,
            'type' => $type,
            // 'is_anonymous' => $isAnonymous,
            'files_json' => json_encode($fileArr),
            'extends_json' => json_encode($extends),
        ];
        // 入库帖子日志表
        $draftId = (new FresnsPostLogs())->store($input);
        if (! empty($idArr)) {
            FresnsFiles::whereIn('id', $idArr)->update(['table_id'=>$draftId]);
        }

        return $draftId;
    }

    // 快速发表创建草稿(comment)
    public static function publishCreatedComment($request)
    {
        $member_id = GlobalService::getGlobalKey('member_id');
        $commentPid = $request->input('commentPid');
        $commentCid = $request->input('commentCid');
        $content = $request->input('content');
        // $isAnonymous = $request->input('isAnonymous',0);
        $isMarkdown = $request->input('isMarkdown');
        $file = request()->file('file');

        $fileInfo = $request->input('fileInfo');
        $eid = $request->input('eid');
        $extends = [];
        // 帖子信息
        $postInfo = FresnsPosts::where('uuid', $commentPid)->first();
        // if(!$postInfo){
        //     LogService::formatInfo("帖子不存在");
        //     return false;
        // }
        // if($commentCid){
        //     $commentInfo = FresnsComments::where('uuid',$commentCid)->first();
        //     if(!$commentInfo){
        //         LogService::formatInfo("评论不存在");
        //         return false;
        //     }
        // }
        // 扩展信息
        $pluginTypeArr = [];
        if ($eid) {
            $eid = json_decode($eid, true);
            foreach ($eid as $e) {
                $arr = [];
                $extendsInfo = FresnsExtends::where('uuid', $e)->first();
                if ($extendsInfo) {
                    $arr['eid'] = $e;
                    $arr['canDelete'] = $extendsInfo['post_id'] ? 'false' : 'true';
                    $arr['rankNum'] = $extendsInfo['rank_num'] ?? 9;
                    $pluginTypeArr[] = $extendsInfo['plugin_unikey'];
                    $extends[] = $arr;
                }
            }
        }
        // dd($pluginInArr);
        $imageType = [];
        $idArr = [];
        if ($file) {
            $idArr = self::publishUpload(2);
            $imageType = ['image'];
        }
        if ($fileInfo) {
            $idArr = self::publishUploadFileInfo($fileInfo);
            $imageType = self::getFileType($fileInfo);
        }
        $fileArr = [];

        if ($idArr) {
            $fileArr = self::getFilesByIdArr($idArr);
        }
        $typeArr = array_unique(array_merge($pluginTypeArr, $imageType));

        if (empty($typeArr)) {
            $type = 'text';
        } else {
            $type = implode(',', $typeArr);
        }
        // dd($type);
        // dd(json_encode($fileArr));
        $content = self::stopWords($content);
        $input = [
            'platform_id' => $request->header('platform'),
            'member_id' => $member_id,
            'type' => $type,
            'post_id' => $postInfo['id'],
            'content' => strip_tags(trim($content)),
            'is_markdown' => $isMarkdown,
            // 'is_anonymous' => $isAnonymous,
            'files_json' => json_encode($fileArr),
            'extends_json' => json_encode($extends),
        ];
        // 入库评论日志表
        $draftId = (new FresnsCommentLogs())->store($input);
        if (! empty($idArr)) {
            FresnsFiles::whereIn('id', $idArr)->update(['table_id'=>$draftId]);
        }

        return $draftId;
    }

    /**
     * 上传文件.
     *
     * @param [type] $type 1-帖子 2-评论
     * @return void
     */
    public static function publishUpload($type)
    {
        $uid = GlobalService::getGlobalKey('user_id');
        $mid = GlobalService::getGlobalKey('member_id');
        $t1 = time();

        if ($type == 1) {
            $tableType = 8;
            $tableName = 'post_logs';
        } else {
            $tableType = 9;
            $tableName = 'comment_logs';
        }

        $unikey = ApiConfigHelper::getConfigByItemKey('images_service');

        $pluginUniKey = $unikey;
        // 执行上传
        $pluginClass = PluginHelper::findPluginClass($pluginUniKey);

        $platformId = request()->header('platform');
        // 确认目录
        $options['file_type'] = 1;
        $options['table_type'] = $tableType;
        $storePath = FileSceneService::getEditorPath($options);
        // 获取UploadFile的实例
        $uploadFile = request()->file('file');

        // 存储
        $path = $uploadFile->store($storePath);
        $file['file_name'] = $uploadFile->getClientOriginalName();
        $file['file_extension'] = $uploadFile->getClientOriginalExtension();
        $file['file_path'] = str_replace('public', '', $path);
        $file['rank_num'] = 9;
        $file['table_type'] = 8;
        $file['file_type'] = 1;
        $file['table_name'] = $tableName;
        $file['table_field'] = 'files_json';

        LogService::info('文件存储本地成功 ', $file);
        $t2 = time();

        $file['uuid'] = ApiCommonHelper::createUuid();
        // 插入
        $retId = FresnsFiles::insertGetId($file);

        $file['real_path'] = $path;
        $input = [
            'file_id' => $retId,
            'file_mime' => $uploadFile->getMimeType(),
            'file_size' => $uploadFile->getSize(),
            'platform_id' => $platformId,
            'transcoding_status' => 1,
            'user_id' => $uid,
            'member_id' => $mid,
            // 'file_original_path' => Storage::url($path),
        ];

        $imageSize = getimagesize($uploadFile);
        $input['image_width'] = $imageSize[0] ?? null;
        $input['image_height'] = $imageSize[1] ?? null;
        $input['image_is_long'] = 0;
        if (! empty($input['image_width']) && ! empty($input['image_height'])) {
            if ($input['image_height'] >= $input['image_width'] * 4) {
                $input['image_is_long'] = 1;
            }
        }

        $file['file_size'] = $input['file_size'];
        FresnsFileAppends::insert($input);

        return [$retId];
    }

    //上传fileInfo
    public static function publishUploadFileInfo($fileInfo)
    {
        $uid = GlobalService::getGlobalKey('user_id');
        $mid = GlobalService::getGlobalKey('member_id');
        $platformId = request()->header('platform');
        $fileInfo = json_decode($fileInfo, true);
        $retIdArr = [];
        if (is_array($fileInfo)) {
            foreach ($fileInfo as $v) {
                $item = [];
                $item['uuid'] = ApiCommonHelper::createUuid();
                $item['file_name'] = $v['name'];
                $item['file_type'] = $v['type'];
                $item['table_type'] = $v['tableType'];
                $item['table_name'] = $v['tableName'];
                $item['table_field'] = $v['tableField'];
                $item['file_extension'] = $v['extension'];
                $item['file_path'] = $v['path'];
                $item['rank_num'] = $v['rankNum'] ?? 9;
                $retId = FresnsFiles::insertGetId($item);
                $retIdArr[] = $retId;
                $append = [];
                $append['file_id'] = $retId;
                $append['user_id'] = $uid;
                $append['member_id'] = $mid;
                $append['file_id'] = $retId;
                $append['file_original_path'] = $v['originalPath'] ?? null;
                $append['file_mime'] = $v['mime'] ?? null;
                $append['file_size'] = $v['size'] ?? null;
                $append['file_md5'] = $v['md5'] ?? null;
                $append['file_sha1'] = $v['sha1'] ?? null;
                $append['image_width'] = empty($v['imageWidth']) ? null : $v['imageWidth'];
                $append['image_height'] = empty($v['imageHeight']) ? null : $v['imageHeight'];
                $imageLong = 0;
                if (! empty($fileInfo['imageLong'])) {
                    $length = strlen($fileInfo['imageLong']);
                    if ($length == 1) {
                        $imageLong = $fileInfo['imageLong'];
                    }
                }
                $append['image_is_long'] = $imageLong;
                $append['video_time'] = empty($v['videoTime']) ? null : $v['videoTime'];
                $append['video_cover'] = empty($v['videoCover']) ? null : $v['videoCover'];
                $append['video_gif'] = empty($v['videoGif']) ? null : $v['videoGif'];
                $append['audio_time'] = empty($v['audioTime']) ? null : $v['audioTime'];
                $append['transcoding_status'] = empty($v['transcodingStatus']) ? 1 : $v['transcodingStatus'];
                $append['platform_id'] = $platformId;
                FresnsFileAppends::insert($append);
            }
        }

        return $retIdArr;
    }

    /**
     * 通过file->id 数组去查询文件信息.
     */
    public static function getFilesByIdArr($idArr)
    {
        $filesArr = FresnsFiles::whereIn('id', $idArr)->get()->toArray();
        $imagesHost = ApiConfigHelper::getConfigByItemKey('images_bucket_domain');
        $imagesRatio = ApiConfigHelper::getConfigByItemKey('images_thumb_ratio');
        $imagesSquare = ApiConfigHelper::getConfigByItemKey('images_thumb_square');
        $imagesBig = ApiConfigHelper::getConfigByItemKey('images_thumb_big');
        $videosHost = ApiConfigHelper::getConfigByItemKey('videos_bucket_domain');
        $audiosHost = ApiConfigHelper::getConfigByItemKey('audios_bucket_domain');
        $docsHost = ApiConfigHelper::getConfigByItemKey('docs_bucket_domain');
        $docsOnlinePreview = ApiConfigHelper::getConfigByItemKey('docs_online_preview');
        $data = [];
        if ($filesArr) {
            foreach ($filesArr as $file) {
                $item = [];
                $append = FresnsFileAppends::where('file_id', $file['id'])->first();
                $type = $file['file_type'];
                $item['fid'] = $file['uuid'];
                $item['type'] = $file['file_type'];
                $item['name'] = $file['file_name'];
                $item['extension'] = $file['file_extension'];
                $item['size'] = $append['file_size'];
                if ($type == 1) {
                    $item['imageWidth'] = $append['image_width'] ?? '';
                    $item['imageHeight'] = $append['image_height'] ?? '';
                    $item['imageLong'] = $file['image_long'] ?? '';
                    $item['imageRatioUrl'] = $imagesHost.$file['file_path'].$imagesRatio;
                    $item['imageSquareUrl'] = $imagesHost.$file['file_path'].$imagesSquare;
                    $item['imageBigUrl'] = $imagesHost.$file['file_path'].$imagesBig;
                }
                if ($type == 2) {
                    $item['videoTime'] = $append['video_time'] ?? '';
                    $item['videoCover'] = $append['video_cover'] ?? '';
                    $item['videoGif'] = $append['video_gif'] ?? '';
                    $item['videoUrl'] = $videosHost.$file['file_path'];
                }
                if ($type == 3) {
                    $item['audioTime'] = $append['audio_time'] ?? '';
                    $item['audioUrl'] = $audiosHost.$file['file_path'];
                    $item['transcodingStatus'] = $append['transcoding_status'];
                }
                if ($type == 4) {
                    $item['docUrl'] = $docsHost.$file['file_path'];
                }
                $item['moreJson'] = json_decode($append['more_json'], true);

                $data[] = $item;
            }
        }

        return $data;
    }

    /**
     * 根据fileInfo获取type.
     */
    public static function getFileType($fileInfo)
    {
        $fileInfo = json_decode($fileInfo, true);
        $res = [];
        if (is_array($fileInfo)) {
            foreach ($fileInfo as $f) {
                $arr = 'image';
                if ($f['type'] == 1) {
                    $arr = 'image';
                }
                if ($f['type'] == 2) {
                    $arr = 'video';
                }
                if ($f['type'] == 3) {
                    $arr = 'audio';
                }
                if ($f['type'] == 4) {
                    $arr = 'doc';
                }
                $res[] = $arr;
            }
        }

        return $res;
    }

    // 过滤词规则
    public static function stopWords($text)
    {
        $stopWordsArr = FresnsStopWords::get()->toArray();

        foreach ($stopWordsArr as $v) {
            $str = strstr($text, $v['word']);
            // dd($str);
            if ($str != false) {
                if ($v['content_mode'] == 2) {
                    $text = str_replace($v['word'], $v['replace_word'], $text);

                    return $text;
                }
            }
        }

        return $text;
    }
}
