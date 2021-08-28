<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// 系统解耦, 快捷方式入口

namespace App\Http\FresnsDb\FresnsComments;

use App\Helpers\StrHelper;
use App\Http\Center\Helper\PluginRpcHelper;
use App\Http\FresnsApi\Content\Resource\FresnsPostResource;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsCmds\FresnsSubPlugin;
use App\Http\FresnsCmds\FresnsSubPluginConfig;
use App\Http\FresnsDb\FresnsCommentAppends\FresnsCommentAppends;
use App\Http\FresnsDb\FresnsCommentAppends\FresnsCommentAppendsConfig;
use App\Http\FresnsDb\FresnsCommentLogs\FresnsCommentLogs;
use App\Http\FresnsDb\FresnsComments\FresnsComments;
use App\Http\FresnsDb\FresnsDomainLinks\FresnsDomainLinks;
use App\Http\FresnsDb\FresnsDomainLinks\FresnsDomainLinksConfig;
use App\Http\FresnsDb\FresnsDomains\FresnsDomains;
use App\Http\FresnsDb\FresnsExtendLinkeds\FresnsExtendLinkedsConfig;
use App\Http\FresnsDb\FresnsExtends\FresnsExtends;
use App\Http\FresnsDb\FresnsFiles\FresnsFiles;
use App\Http\FresnsDb\FresnsHashtagLinkeds\FresnsHashtagLinkeds;
use App\Http\FresnsDb\FresnsHashtagLinkeds\FresnsHashtagLinkedsConfig;
use App\Http\FresnsDb\FresnsHashtags\FresnsHashtags;
use App\Http\FresnsDb\FresnsMembers\FresnsMembers;
use App\Http\FresnsDb\FresnsMembers\FresnsMembersConfig;
use App\Http\FresnsDb\FresnsMemberStats\FresnsMemberStats;
use App\Http\FresnsDb\FresnsPosts\FresnsPosts;
use App\Http\FresnsDb\FresnsSessionLogs\FresnsSessionLogs;
use App\Http\FresnsDb\FresnsStopWords\FresnsStopWords;
use App\Http\Center\Common\LogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FresnsCommentsService extends AmService
{
    public function getCommentPreviewList($comment_id, $limit, $mid)
    {
        $AmService = new AmService();
        // dd($comments['id']);
        request()->offsetSet('id', $comment_id);
        $data = $AmService->listTreeNoRankNum();
        $data = $AmService->treeData();
        // dd($data);
        // 获取childrenIdArr
        $childrenIdArr = [];
        if ($data) {
            foreach ($data as $v) {
                $this->getChildrenIds($v, $childrenIdArr);
            }
            // dd($childrenIdArr);
        }
        array_unshift($childrenIdArr, $comment_id);
        // dd($childrenIdArr);
        request()->offsetUnset('id');
        // dd($childrenIdArr);
        // $query->where('comment.id','=',$comments['id']);
        // 未被删除用户的评论
        $memberArr = FresnsMembers::where('deleted_at', null)->pluck('id')->toArray();
        $comments = FresnsComments::whereIn('member_id', $memberArr)->whereIn('id', $childrenIdArr)->where('parent_id',
            '!=', 0)->orderBy('like_count', 'desc')->limit($limit)->get();
        //    dd($comments);
        $result = [];
        if ($comments) {
            foreach ($comments as $v) {
                $memberInfo = FresnsMembers::find($v['member_id']);
                $arr = [];
                $arr['anonymous'] = $v['is_anonymous'];
                $arr['isAuthor'] = '';
                $arr['mid'] = '';
                $arr['mname'] = '';
                $arr['nickname'] = '';
                if ($v['is_anonymous'] == 0) {
                    $arr['isAuthor'] = $v['member_id'] == $mid ? true : false;
                    $arr['mid'] = $memberInfo['uuid'];
                    $arr['mname'] = $memberInfo['name'];
                    $arr['nickname'] = $memberInfo['nickname'];
                    $arr['avatar'] = $memberInfo['avatar_file_url'];
                    $arr['cid '] = $v['uuid'];
                    // $arr['content '] = $v['content'];
                    $arr['content '] = FresnsPostResource::getContentView($v['content'], $comment_id, 2);
                    $attachCount = [];
                    $attachCount['image'] = FresnsFiles::where('file_type', 2)->where('table_name',
                        FresnsCommentsConfig::CFG_TABLE)->where('table_id', $v['id'])->count();
                    $attachCount['videos'] = FresnsFiles::where('file_type', 3)->where('table_name',
                        FresnsCommentsConfig::CFG_TABLE)->where('table_id', $v['id'])->count();
                    $attachCount['audios'] = FresnsFiles::where('file_type', 4)->where('table_name',
                        FresnsCommentsConfig::CFG_TABLE)->where('table_id', $v['id'])->count();
                    $attachCount['docs'] = FresnsFiles::where('file_type', 5)->where('table_name',
                        FresnsCommentsConfig::CFG_TABLE)->where('table_id', $v['id'])->count();
                    $attachCount['extends'] = Db::table(FresnsExtendLinkedsConfig::CFG_TABLE)->where('linked_type',
                        2)->where('linked_id', $v['id'])->count();
                    $arr['attachCount'] = $attachCount;
                    $replyTo = [];
                    // replyTo 该条回复的 parent_id = 当前评论的 ID，则不输出以下信息。当前评论 ID 则代表二级评论。
                    $replyComment = FresnsComments::where('id', $v['parent_id'])->orderBy('like_count',
                        'desc')->first();
                    // 回复用户信息
                    if (! empty($replyComment) && ($v['parent_id'] != $comment_id)) {
                        $replyMEmberInfo = FresnsMembers::find($replyComment['member_id']);
                        $replyTo['cid'] = $replyComment['uuid'] ?? '';
                        $replyTo['anonymous'] = $replyComment['uuid'] ?? '';
                        $replyTo['deactivate'] = $replyComment['deleted_at'] == null ? true : false;
                        $replyTo['mid'] = $replyMEmberInfo['uuid'] ?? '';
                        $replyTo['mname'] = $replyMEmberInfo['name'] ?? '';
                        $replyTo['nickname'] = $replyMEmberInfo['nickname'] ?? '';
                        $arr['replyTo'] = $replyTo;
                    }
                    $result[] = $arr;
                }
            }
        }

        return $result;
    }

    // 获取replty
    public function getReplyToPreviewList($comment_id, $mid)
    {
        $searchCid = request()->input('searchCid');
        $commentCid = FresnsComments::where('uuid', $searchCid)->first();
        $AmService = new AmService();
        // dump($comment_id);
        request()->offsetSet('id', $comment_id);
        $data = $AmService->listTreeNoRankNum();
        $data = $AmService->treeData();
        // dd($data);
        // 获取childrenIdArr
        $childrenIdArr = [];
        if ($data) {
            foreach ($data as $v) {
                $this->getChildrenIds($v, $childrenIdArr);
            }
            // dd($childrenIdArr);
        }
        // dd($childrenIdArr);
        array_unshift($childrenIdArr, $comment_id);
        // dd($childrenIdArr);
        request()->offsetUnset('id');
        $replyTo = [];
        $comments = FresnsComments::whereIn('id', $childrenIdArr)->where('parent_id', '!=',
            $commentCid['id'])->where('parent_id', '!=', 0)->orderBy('like_count', 'desc')->get();
        // dd($comments);
        if ($comments) {
            foreach ($comments as $c) {
                $reply = [];
                if ($c['parent_id'] != $comment_id) {
                    $parentCommentInfo = FresnsComments::find($c['parent_id']);
                    if ($parentCommentInfo) {
                        $parentMemberInfo = DB::table(FresnsMembersConfig::CFG_TABLE)->where('id', $parentCommentInfo['member_id'])->first();
                    }
                    $reply['cid'] = $parentCommentInfo['uuid'] ?? '';
                    $reply['anonymous'] = $parentCommentInfo['is_anonymous'];
                    $reply['deactivate'] = false;
                    $reply['mid'] = '';
                    $reply['mname'] = '';
                    $reply['nickname'] = '';
                    if ($parentCommentInfo['is_anonymous'] == 0) {
                        if ($parentMemberInfo->deleted_at == null) {
                            $reply['deactivate'] = true;
                            $reply['mid'] = $parentMemberInfo->uuid ?? '';
                            $reply['mname'] = $parentMemberInfo->name ?? '';
                            $reply['nickname'] = $parentMemberInfo->nickname ?? '';
                        }
                    }
                    $replyTo[] = $reply;
                }
            }
        }
        // dd($replyTo);
        return $replyTo;
    }

    // 发表评论
    public function releaseByDraft($draftId, $commentCid = 0, $sessionLodsId = 0)
    {
        // 直接发表
        $releaseResult = $this->doRelease($draftId, $commentCid, $sessionLodsId);
        if (! $releaseResult) {
            LogService::formatInfo('评论发布异常');

            return false;
        }

        return $releaseResult;
    }

    // 发表
    public function doRelease($draftId, $commentCid = 0, $sessionLodsId)
    {
        // s判断是更新还是新增
        $draftComment = FresnsCommentLogs::find($draftId);
        if (! $draftComment) {
            LogService::formatInfo('评论草稿不存在');

            return false;
        }
        // $this->sendAtMessages(10,$draftId);
        // 新增
        if (! $draftComment['comment_id']) {
            // dd(1);
            $res = $this->storeToDb($draftId, $commentCid, $sessionLodsId);
        } else {
            // 编辑
            $res = $this->updateTob($draftId, $sessionLodsId);
        }

        return true;
    }

    // 入库
    public function storeToDb($draftId, $commentCid = 0, $sessionLodsId = 0)
    {
        // 解析基础信息
        $draftComment = FresnsCommentLogs::find($draftId);
        // $baseInfoArr = $this->parseDraftBaseInfo($draftId);
        // dump($baseInfoArr);
        // 解析内容信息(判断内容是否需要截断)
        $contentBrief = $this->parseDraftContent($draftId);
        // dd($contentBrief);
        $uuid = strtolower(StrHelper::randString(8));
        // 获取评论的摘要字数
        $commentEditorbRIEFCount = ApiConfigHelper::getConfigByItemKey(AmConfig::COMMENT_EDITOR_WORD_COUNT) ?? 280;
        if (mb_strlen($draftComment['content']) > $commentEditorbRIEFCount) {
            $is_brief = 1;
        } else {
            $is_brief = 0;
        }
        $allosJsonDecode = json_decode($draftComment['allow_json'], true);
        $is_allow = $allosJsonDecode['isAllow'] ?? 0;
        // 位置信息配置
        $locationJson = json_decode($draftComment['location_json'], true);
        $isLbs = $locationJson['isLbs'] ?? 0;
        $more_json = [];
        $more_json['files'] = json_decode($draftComment['files_json'], true);
        LogService::info('draftComment', $draftComment);
        LogService::info('more_json', $more_json);
        $postInput = [
            'uuid' => $uuid,
            'member_id' => $draftComment['member_id'],
            'post_id' => $draftComment['post_id'],
            'type' => $draftComment['type'],
            'content' => $contentBrief,
            'is_brief' => $is_brief,
            'parent_id' => $commentCid,
            // 'is_markdown' => $draftComment['is_markdown'],
            'is_anonymous' => $draftComment['is_anonymous'],
            // 'status' => 3,
            'is_lbs' => $isLbs,
            // 'release_at'  => date('Y-m-d H:i:s'),
            'more_json' => json_encode($more_json),
        ];
        LogService::info('postInput', $postInput);

        // $commentId = DB::table('comments')->insertGetId($postInput);
        $commentId = (new FresnsComments())->store($postInput);
        $AppendStore = $this->postAppendStore($commentId, $draftId);
        if ($AppendStore) {
            FresnsSessionLogs::where('id', $sessionLodsId)->update([
                'object_result' => 2,
                'object_order_id' => $commentId,
            ]);
            // 入库后执行相应操作
            $this->afterStoreToDb($commentId, $draftId);
        }
    }

    // 编辑
    public function updateTob($draftId, $sessionLodsId)
    {
        $draftComment = FresnsCommentLogs::find($draftId);
        $comment = FresnsComments::find($draftComment['comment_id']);
        FresnsSessionLogs::where('id', $sessionLodsId)->update([
            'object_result' => 2,
            'object_order_id' => $draftComment['comment_id'],
        ]);
        // 解析内容信息(判断内容是否需要截断)
        $contentBrief = $this->parseDraftContent($draftId);
        // dd($contentBrief);
        // 获取评论的摘要字数
        $commentEditorbRIEFCount = ApiConfigHelper::getConfigByItemKey(AmConfig::COMMENT_EDITOR_WORD_COUNT) ?? 280;
        if (mb_strlen($draftComment['content']) > $commentEditorbRIEFCount) {
            $is_brief = 1;
        } else {
            $is_brief = 0;
        }
        $allosJsonDecode = json_decode($draftComment['allow_json'], true);
        $is_allow = $allosJsonDecode['isAllow'] ?? 0;
        // 位置信息配置
        $locationJson = json_decode($draftComment['location_json'], true);
        $isLbs = $locationJson['isLbs'] ?? '';
        $more_json = [];
        $more_json['files'] = json_decode($draftComment['files_json'], true);

        $commentInput = [
            'type' => $draftComment['type'],
            'content' => $contentBrief,
            'is_brief' => $is_brief,
            // 'is_markdown' => $draftComment['is_markdown'],
            'is_anonymous' => $draftComment['is_anonymous'],
            'is_lbs' => $isLbs,
            'latest_edit_at' => date('Y-m-d H:i:s'),
            'more_json' => $more_json,
        ];
        FresnsComments::where('id', $draftComment['comment_id'])->update($commentInput);
        $AppendStore = $this->commentAppendUpdate($draftComment['comment_id'], $draftId);
        if ($AppendStore) {
            // 入库后执行相应操作
            $this->afterUpdateToDb($draftComment['comment_id'], $draftId);
        }
    }

    // 副表(新增)
    public function postAppendStore($commentId, $draftId)
    {
        $draftComment = FresnsCommentLogs::find($draftId);
        // 副表
        // 编辑器配置
        $pluginEdit = $draftComment['is_plugin_edit'];
        $pluginUnikey = $draftComment['plugin_unikey'];
        // 位置信息配置
        $locationJson = json_decode($draftComment['location_json'], true);
        $mapId = $locationJson['mapId'] ?? null;
        $latitude = $locationJson['latitude'] ?? null;
        $longitude = $locationJson['longitude'] ?? null;
        $scale = $locationJson['scale'] ?? null;
        $poi = $locationJson['poi'] ?? null;
        $poiId = $locationJson['poiId'] ?? null;
        $nation = $locationJson['nation'] ?? null;
        $province = $locationJson['province'] ?? null;
        $city = $locationJson['city'] ?? null;
        $district = $locationJson['district'] ?? null;
        $adcode = $locationJson['adcode'] ?? null;
        $address = $locationJson['address'] ?? null;
        // 扩展信息
        $extendsJson = json_decode($draftComment['extends_json'], true);
        if ($extendsJson) {
            // 先清空
            // Db::table('extend_linkeds')->where('linked_type',2)->where('linked_id',$commentId)->delete();
            foreach ($extendsJson as $e) {
                $extend = FresnsExtends::where('uuid', $e['eid'])->first();
                if ($extend) {
                    $input = [
                        'linked_type' => 2,
                        'linked_id' => $commentId,
                        'extend_id' => $extend['id'],
                        'plugin_unikey' => $extend['plugin_unikey'] ?? '',
                        'rank_num' => $e['rankNum'],
                    ];
                    Db::table('extend_linkeds')->insert($input);
                }
            }
        }
        $content = $draftComment['content'];
        $content = $this->stopWords($content);
        // 去除html标签
        $content = strip_tags($content);
        $commentAppendInput = [
            'comment_id' => $commentId,
            'platform_id' => $draftComment['platform_id'],
            'content' => $content,
            'is_markdown' => $draftComment['is_markdown'],
            'is_plugin_edit' => $pluginEdit,
            'plugin_unikey' => $pluginUnikey,
            'map_id' => $mapId,
            'map_latitude' => $latitude,
            'map_longitude' => $longitude,
            'map_scale' => $scale,
            'map_poi' => $poi,
            'map_poi_id' => $poiId,
            'map_nation' => $nation,
            'map_province' => $province,
            'map_city' => $city,
            'map_district' => $district,
            'map_adcode' => $adcode,
            'map_address' => $address,
        ];
        DB::table(FresnsCommentAppendsConfig::CFG_TABLE)->insert($commentAppendInput);

        return true;
    }

    // 副表（编辑）
    public function commentAppendUpdate($commentId, $draftId)
    {
        $draftComment = FresnsCommentLogs::find($draftId);
        // 编辑器配置
        $pluginEdit = $draftComment['is_plugin_edit'];
        $pluginUnikey = $draftComment['plugin_unikey'];
        // 位置信息配置
        $locationJson = json_decode($draftComment['location_json'], true);
        $mapId = $locationJson['mapId'] ?? null;
        $latitude = $locationJson['latitude'] ?? null;
        $longitude = $locationJson['longitude'] ?? null;
        $scale = $locationJson['scale'] ?? null;
        $poi = $locationJson['poi'] ?? null;
        $poiId = $locationJson['poiId'] ?? null;
        $nation = $locationJson['nation'] ?? null;
        $province = $locationJson['province'] ?? null;
        $city = $locationJson['city'] ?? null;
        $district = $locationJson['district'] ?? null;
        $adcode = $locationJson['adcode'] ?? null;
        $address = $locationJson['address'] ?? null;
        // 扩展信息
        $extendsJson = json_decode($draftComment['extends_json'], true);
        if ($extendsJson) {
            // 先清空
            Db::table('extend_linkeds')->where('linked_type', 2)->where('linked_id', $commentId)->delete();
            foreach ($extendsJson as $e) {
                $extend = FresnsExtends::where('uuid', $e['eid'])->first();
                if ($extend) {
                    $input = [
                        'linked_type' => 2,
                        'linked_id' => $commentId,
                        'extend_id' => $extend['id'],
                        'plugin_unikey' => $extend['plugin_unikey'] ?? '',
                        'rank_num' => $e['rankNum'] ?? 9,
                    ];
                    Db::table('extend_linkeds')->insert($input);
                }
            }
        }
        $content = $draftComment['content'];
        $content = $this->stopWords($content);
        // 去除html标签
        $content = strip_tags($content);
        $commentAppendInput = [
            'platform_id' => $draftComment['platform_id'],
            'content' => $content,
            'is_plugin_edit' => $pluginEdit,
            'plugin_unikey' => $pluginUnikey,
            'map_id' => $mapId,
            'map_latitude' => $latitude,
            'map_longitude' => $longitude,
            'map_scale' => $scale,
            'map_poi' => $poi,
            'map_poi_id' => $poiId,
            'map_nation' => $nation,
            'map_province' => $province,
            'map_city' => $city,
            'map_district' => $district,
            'map_adcode' => $adcode,
            'map_address' => $address,
        ];
        FresnsCommentAppends::where('comment_id', $commentId)->update($commentAppendInput);

        return true;
    }

    // 入库后执行相应操作
    public function afterStoreToDb($commentId, $draftId)
    {
        // 调用插件订阅命令字
        $cmd = FresnsSubPluginConfig::PLG_CMD_SUB_ADD_TABLE;
        $input = [
            'tableName' => FresnsCommentsConfig::CFG_TABLE,
            'insertId' => $commentId,
        ];
        LogService::info('table_input', $input);
        // dd($input);
        PluginRpcHelper::call(FresnsSubPlugin::class, $cmd, $input);
        $draftComment = FresnsCommentLogs::find($draftId);
        $content = $this->stopWords($draftComment['content']);
        // 草稿更新为已发布
        FresnsCommentLogs::where('id', $draftId)->update(['status' => 3, 'comment_id' => $commentId, 'content' => $content]);
        $this->sendAtMessages($commentId, $draftId);
        $this->sendCommentMessages($commentId, $draftId);
        // $this->fillDbInfo($draftId);
        // 	我的 member_stats > post_publish_count
        $this->memberStats($draftId);
        // 解析话题
        $this->analisisHashtag($draftId, 1);
        $this->domainStore($commentId, $draftId);

        //dd($res);
        //  配置表键值 post_counts
        return true;
    }

    // 入库后执行相应操作（编辑）
    public function afterUpdateToDb($commentId, $draftId)
    {
        // 调用插件订阅命令字
        $cmd = FresnsSubPluginConfig::PLG_CMD_SUB_ADD_TABLE;
        $input = [
            'tableName' => FresnsCommentsConfig::CFG_TABLE,
            'insertId' => $commentId,
        ];
        LogService::info('table_input', $input);
        // dd($input);
        PluginRpcHelper::call(FresnsSubPlugin::class, $cmd, $input);
        $draftComment = FresnsCommentLogs::find($draftId);
        $content = $this->stopWords($draftComment['content']);
        // 草稿更新为已发布
        FresnsCommentLogs::where('id', $draftId)->update(['status' => 3, 'content'=> $content]);

        FresnsCommentAppends::where('comment_id', $commentId)->increment('edit_count');

        $this->sendAtMessages($commentId, $draftId, 2);
        $this->sendCommentMessages($commentId, $draftId);
        // $this->fillDbInfo($draftId);
        // 	我的 member_stats > post_publish_count
        // 解析话题
        $this->analisisHashtag($draftId, 2);
        $this->domainStore($commentId, $draftId, 2);
        //dd($res);
        //  配置表键值 post_counts
        return true;
    }

    // 不能艾特自己，艾特别人则给对方产生一条通知消息。
    // 调用 MessageService 处理
    public function sendAtMessages($commentId, $draftId, $updateType = 1)
    {
        $draftComment = FresnsCommentLogs::find($draftId);
        $commentInfo = FresnsComments::find($commentId);
        // if ($updateType == 2) {
        //     DB::table('mentions')->where('linked_type', 2)->where('linked_id', $commentId)->delete();
        // }
        preg_match_all("/@.*?\s/", $draftComment['content'], $atMatches);
        // 存在发送消息
        // dd($atMatches);
        if ($atMatches[0]) {
            foreach ($atMatches[0] as $s) {
                // dd($s);
                // 查询接受用户id
                $name = trim(ltrim($s, '@'));
                // dd($name);
                $memberInfo = FresnsMembers::where('name', $name)->first();
                if ($memberInfo && $memberInfo['id'] != $draftComment['member_id']) {
                    $input = [
                        'source_id' => $commentId,
                        'source_brief' => $commentInfo['content'],
                        'member_id' => $memberInfo['id'],
                        'source_member_id' => $commentInfo['member_id'],
                        'source_type' => 5,
                        'source_class' => 2,
                    ];
                    DB::table('notifies')->insert($input);
                    //  艾特记录表
                    $mentions = [
                        'member_id' => $commentInfo['member_id'],
                        'linked_type' => 2,
                        'linked_id' => $commentId,
                        'mention_member_id' => $memberInfo['id'],
                    ];
                    $count = DB::table('mentions')->where($mentions)->count();
                    if ($count == 0) {
                        DB::table('mentions')->insert($mentions);
                    }
                }
            }
        }

        return true;
    }

    // 发表成功后，帖子或者评论的主键 ID 产生，然后把 ID 填到 files > table_id 字段里，补齐信息。
    public function fillDbInfo($draftId)
    {
        $draftComment = FresnsCommentLogs::find($draftId);
        $fileArr = json_decode($draftComment['files_json'], true);
        if ($fileArr) {
            foreach ($fileArr as $f) {
                $fileCount = FresnsFiles::where('uuid', $f['fid'])->count();
                if ($fileCount > 0) {
                    // FresnsFiles::where('uuid', $f['fid'])->update(['table_id' => $draftComment['comment_id']]);
                    FresnsFiles::where('uuid', $f['fid'])->update(['table_id' => $draftId]);
                }
            }
        }

        return true;
    }

    // 我的 member_stats > post_publish_count
    // 配置表键值 post_counts
    public function memberStats($draftId)
    {
        $draftComment = FresnsCommentLogs::find($draftId);
        $memberStats = FresnsMemberStats::where('member_id', $draftComment['member_id'])->first();
        if ($memberStats) {
            FresnsMemberStats::where('id', $memberStats['id'])->increment('comment_publish_count');
        } else {
            (new FresnsMemberStats())->store(['member_id' => $draftComment['member_id'], 'comment_publish_count' => 1]);
        }
        DB::table('configs')->where('item_key', AmConfig::COMMENT_COUNTS)->increment('item_value');

        return true;
    }

    // 评论则判断父级是否为自己，不是自己则为对方产生一条通知。一级评论给帖子作者（帖子作者不是自己）产生通知。
    // 调用 MessageService 处理
    public function sendCommentMessages($commentId, $draftId)
    {
        $draftComment = FresnsCommentLogs::find($draftId);
        $postInfo = FresnsPosts::find($draftComment['post_id']);
        $comment = FresnsComments::where('id', $draftComment['comment_id'])->first();
        // 一级评论给帖子作者（帖子作者不是自己）产生通知。
        if (($draftComment['member_id'] != $postInfo['member_id']) && $comment['parent_id'] == 0) {
            FresnsComments::where('id', $commentId)->increment('comment_count');
            $input = [
                'source_id' => $commentId,
                'source_brief' => $draftComment['content'],
                'member_id' => $postInfo['member_id'],
                'source_member_id' => $draftComment['member_id'],
                'source_type' => 4,
                'source_class' => 1,
            ];
            DB::table('notifies')->insert($input);
        }
        // 评论则判断父级是否为自己，不是自己则为对方产生一条通知
        if ($comment['parent_id'] != 0 && ($comment['parent_id'] != $draftComment['member_id'])) {
            FresnsComments::where('id', $comment['parent_id'])->increment('comment_count');
            $input = [
                'source_id' => $commentId,
                'source_brief' => $draftComment['content'],
                'member_id' => $postInfo['member_id'],
                'source_member_id' => $draftComment['member_id'],
                'source_type' => 4,
                'source_class' => 2,
            ];
            DB::table('notifies')->insert($input);
        }

        return true;
    }

    // 域名链接表
    public function domainStore($commentId, $draftId, $updateType = 1)
    {
        $draftComment = FresnsCommentLogs::find($draftId);
        if ($updateType == 2) {
            $domainLinksIdArr = FresnsDomainLinks::where('linked_type', 1)->where('linked_id', $commentId)->pluck('domain_id')->toArray();
            FresnsDomains::where('id', $domainLinksIdArr)->decrement('post_count');
            DB::table(FresnsDomainLinksConfig::CFG_TABLE)->where('linked_type', 2)->where('linked_id', $commentId)->delete();
        }
        // $postInfo = FresnsPosts::find($postId);
        preg_match_all("/http[s]{0,1}:\/\/.*?\s/", $draftComment['content'], $hrefMatches);
        if ($hrefMatches[0]) {
            foreach ($hrefMatches[0] as $h) {
                $firstDomain = $this->top_domain(trim($h));
                // 二级域名
                $domain = $this->regular_domain(trim($h));
                preg_match('/(.*\.)?\w+\.\w+$/', $domain, $secDomain);
                // 域名表是否存在
                $domain_input = [
                    'domain' => $firstDomain,
                    'sld' => $secDomain[0],
                ];
                $domainInfo = FresnsDomains::where($domain_input)->first();
                if ($domainInfo) {
                    $domainId = $domainInfo['id'];
                    FresnsDomains::where('id', $domainId)->increment('comment_count');
                } else {
                    $domainId = (new FresnsDomains())->store($domain_input);
                    FresnsDomains::where('id', $domainId)->increment('comment_count');
                }
                $input = [
                    'linked_type' => 2,
                    'linked_id' => $commentId,
                    'link_url' => trim($h),
                    'domain_id' => $domainId,
                ];
                $domainLinkCount = DB::table('domain_links')->where($input)->count();
                if ($domainLinkCount == 0) {
                    DB::table('domain_links')->insert($input);
                }
            }
        }

        return true;
    }

    // 解析话题(入库话题表)
    public function analisisHashtag($draftId, $type = 1)
    {
        $draftComment = FresnsCommentLogs::find($draftId);
        // $draftPost['content'] = "这里是1帖子@成员3 的文本。<a onclick='return false;' href='http://www.baidu.com'>点击跳转百度</a>#话题 5##话题2#@成员2";
        // dump($draftPost['content']);
        // 当前后台话题的显示模式
        $hashtagShow = ApiConfigHelper::getConfigByItemKey(AmConfig::HASHTAG_SHOW) ?? 2;
        if ($hashtagShow == 1) {
            preg_match_all("/#.*?\s/", $draftComment['content'], $singlePoundMatches);
        } else {
            preg_match_all('/#.*?#/', $draftComment['content'], $singlePoundMatches);
        }
        // dd($singlePoundMatches);
        if ($type == 2) {
            // 去除话题关联
            // DB::table(FresnsHashtagLinkedsConfig::CFG_TABLE)->where('linked_type', 2)->where('linked_id',$draftComment['comment_id'])->delete();
            $hashtagIdArr = FresnsHashtagLinkeds::where('linked_type', 2)->where('linked_id', $draftComment['comment_id'])->pluck('hashtag_id')->toArray();
            FresnsHashtags::whereIn('id', $hashtagIdArr)->decrement('comment_count');
            FresnsHashtagLinkeds::where('linked_type', 2)->where('linked_id', $draftComment['post_id'])->delete();
        }
        if ($singlePoundMatches[0]) {
            foreach ($singlePoundMatches[0] as $s) {
                // 将话题的#号去掉
                $s = trim(str_replace('#', '', $s));
                // 是否存在话题
                $hashInfo = FresnsHashtags::where('name', $s)->first();
                if ($hashInfo) {
                    // 话题表comment_count +1
                    FresnsHashtags::where('id', $hashInfo['id'])->increment('comment_count');
                    // 建立关联关系
                    $res = DB::table(FresnsHashtagLinkedsConfig::CFG_TABLE)->insert([
                        'linked_type' => 2,
                        'linked_id' => $draftComment['comment_id'],
                        'hashtag_id' => $hashInfo['id'],
                    ]);
                } else {
                    // 新建话题和话题关联
                    $slug = urlencode($s);
                    $input = [
                        'slug' => $slug,
                        'name' => $s,
                        'comment_count' => 1,
                    ];
                    $hashtagId = (new FresnsHashtags())->store($input);
                    // 建立关联关系
                    $res = DB::table(FresnsHashtagLinkedsConfig::CFG_TABLE)->insert([
                        'linked_type' => 2,
                        'linked_id' => $draftComment['comment_id'],
                        'hashtag_id' => $hashtagId,
                    ]);
                    DB::table('configs')->where('item_key', AmConfig::HASHTAG_COUNTS)->increment('item_value');
                }
            }
        }
        // dd($res);
        return true;
        // dd($singlePoundMatches);
    }

    // 解析截断内容信息
    public function parseDraftContent($draftId)
    {
        $draftComment = FresnsCommentLogs::find($draftId);
        $content = $draftComment['content'];
        // 获取帖子的上线字数
        // $commentEditorWordCount = ApiConfigHelper::getConfigByItemKey(AmConfig::COMMENT_EDITOR_WORD_COUNT) ?? 1000;
        // 获取帖子的摘要字数
        $commentEditorbRIEFCount = ApiConfigHelper::getConfigByItemKey(AmConfig::COMMENT_EDITOR_BRIEF_COUNT) ?? 280;
        if (mb_strlen(trim($draftComment['content'])) > $commentEditorbRIEFCount) {
            $contentInfo = $this->truncatedContentInfo($content, $commentEditorbRIEFCount);
            $content = $contentInfo['truncated_content'];
        } else {
            $content = $draftComment['content'];
        }
        $content = $this->stopWords($content);
        // if(mb_strlen($content) > $commentEditorWordCount){
        // }
        return $content;
    }

    // “艾特”、“话题”、“链接” content全文中三者的位置信息
    // 内容超过设置的字数时，需要摘要存储，如果摘要最后内容是“艾特”、“话题”、“链接”三种信息，要留全，不能截断，保全时可以限定字数。
    public function truncatedContentInfo($content, $wordCount = 280)
    {
        // 当前后台话题的显示模式
        $hashtagShow = ApiConfigHelper::getConfigByItemKey(AmConfig::HASHTAG_SHOW) ?? 2;
        // $content = "这里是1帖子@刘liuliu 的文本。https://tangjie.me #话题1 12345#话题 2#";
        // 在 $content 中匹配 位置信息,  这里的正则放到配置文件中
        if ($hashtagShow == 1) {
            preg_match("/#.*?\s/", $content, $singlePoundMatches, PREG_OFFSET_CAPTURE);
        } else {
            preg_match('/#.*?#/', $content, $singlePoundMatches, PREG_OFFSET_CAPTURE);
        }
        /**
         * preg_match("/<a .*?>.*?<\/a>/",$content,$hrefMatches,PREG_OFFSET_CAPTURE);.
         *  */
        preg_match("/http[s]:\/\/.*?\s/", $content, $hrefMatches, PREG_OFFSET_CAPTURE);
        // dd($singlePoundMatches);
        // dd($hrefMatches);
        // preg_match("/<a href=.*?}></a>/", $content, $hrefMatches,PREG_OFFSET_CAPTURE);
        preg_match("/@.*?\s/", $content, $atMatches, PREG_OFFSET_CAPTURE);
        $truncatedPos = $wordCount;
        $findTruncatedPos = false;
        // 判断这个wordCount落在的区间位置， 如果有命中，则找到对应的截断位置，并执行截断
        // https://www.php.net/manual/en/function.preg-match.php
        foreach ($singlePoundMatches as $currMatch) {
            $matchStr = $currMatch[0];
            $matchStrStartPosition = $currMatch[1];
            $matchStrEndPosition = $currMatch[1] + strlen($matchStr);
            // 命中
            if ($matchStrStartPosition <= $wordCount && $matchStrEndPosition >= $wordCount) {
                $findTruncatedPos = true;
                $truncatedPos = $matchStrEndPosition;
            }
        }
        // [1,4] [6,9] [15,33] [41,45], [50,77]
        // [1,4] [6,9] [15,33] [41,45], [65,69]
        //  adjfaljdfsdfidksieijsdfasdf@cccc

        // // 如果未发现则继续匹配
        // if(!$findTruncatedPos){
        //     foreach ($singlePoundMatches as $currMatch) {
        //         $matchStr = $currMatch[0];
        //         $matchStrStartPosition = $currMatch[1];
        //         $matchStrEndPosition = $currMatch[1] + strlen($matchStr);
        //         // 命中
        //         if ($matchStrStartPosition <= $wordCount && $matchStrEndPosition >= $wordCount) {
        //             $findTruncatedPos = true;
        //             $truncatedPos = $matchStrEndPosition;
        //         }
        //     }
        // }

        if (! $findTruncatedPos) {
            foreach ($hrefMatches as $currMatch) {
                $matchStr = $currMatch[0];
                $matchStrStartPosition = $currMatch[1];
                $matchStrEndPosition = $currMatch[1] + strlen($matchStr);
                // 命中
                if ($matchStrStartPosition <= $wordCount && $matchStrEndPosition >= $wordCount) {
                    $findTruncatedPos = true;
                    $truncatedPos = $matchStrEndPosition;
                }
            }
        }
        if (! $findTruncatedPos) {
            foreach ($atMatches as $currMatch) {
                $matchStr = $currMatch[0];
                $matchStrStartPosition = $currMatch[1];
                $matchStrEndPosition = $currMatch[1] + mb_strlen($matchStr);
                // 命中
                if ($matchStrStartPosition <= $wordCount && $matchStrEndPosition >= $wordCount) {
                    $findTruncatedPos = true;
                    $truncatedPos = $matchStrEndPosition;
                }
            }
        }

        // 执行操作
        $info = [];
        $info['find_truncated_pos'] = $findTruncatedPos;
        $info['truncated_pos'] = $truncatedPos;  // 截断位置
        $info['truncated_content'] = Str::substr($content, 0, $truncatedPos); // 最终内容
        // $info['double_pound_arr'] = $doublePoundMatches;
        $info['single_pound_arr'] = $singlePoundMatches;
        $info['link_pound_arr'] = $hrefMatches;
        $info['at_arr'] = $atMatches;

        return $info;
    }

    public function regular_domain($domain)
    {
        if (substr($domain, 0, 7) == 'http://') {
            $domain = substr($domain, 7);
        }
        if (substr($domain, 0, 8) == 'https://') {
            $domain = substr($domain, 8);
        }
        if (strpos($domain, '/') !== false) {
            $domain = substr($domain, 0, strpos($domain, '/'));
        }

        return strtolower($domain);
    }

    public function top_domain($domain)
    {
        $domain = $this->regular_domain($domain);
        //   dd($domain);
        $iana_root = [
            'ac',
            'ad',
            'ae',
            'aero',
            'af',
            'ag',
            'ai',
            'al',
            'am',
            'an',
            'ao',
            'aq',
            'ar',
            'arpa',
            'as',
            'asia',
            'at',
            'au',
            'aw',
            'ax',
            'az',
            'ba',
            'bb',
            'bd',
            'be',
            'bf',
            'bg',
            'bh',
            'bi',
            'biz',
            'bj',
            'bl',
            'bm',
            'bn',
            'bo',
            'bq',
            'br',
            'bs',
            'bt',
            'bv',
            'bw',
            'by',
            'bz',
            'ca',
            'cat',
            'cc',
            'cd',
            'cf',
            'cg',
            'ch',
            'ci',
            'ck',
            'cl',
            'cm',
            'cn',
            'co',
            'com',
            'coop',
            'cr',
            'cu',
            'cv',
            'cw',
            'cx',
            'cy',
            'cz',
            'de',
            'dj',
            'dk',
            'dm',
            'do',
            'dz',
            'ec',
            'edu',
            'ee',
            'eg',
            'eh',
            'er',
            'es',
            'et',
            'eu',
            'fi',
            'fj',
            'fk',
            'fm',
            'fo',
            'fr',
            'ga',
            'gb',
            'gd',
            'ge',
            'gf',
            'gg',
            'gh',
            'gi',
            'gl',
            'gm',
            'gn',
            'gov',
            'gp',
            'gq',
            'gr',
            'gs',
            'gt',
            'gu',
            'gw',
            'gy',
            'hk',
            'hm',
            'hn',
            'hr',
            'ht',
            'hu',
            'id',
            'ie',
            'il',
            'im',
            'in',
            'info',
            'int',
            'io',
            'iq',
            'ir',
            'is',
            'it',
            'je',
            'jm',
            'jo',
            'jobs',
            'jp',
            'ke',
            'kg',
            'kh',
            'ki',
            'km',
            'kn',
            'kp',
            'kr',
            'kw',
            'ky',
            'kz',
            'la',
            'lb',
            'lc',
            'li',
            'lk',
            'lr',
            'ls',
            'lt',
            'lu',
            'lv',
            'ly',
            'ma',
            'mc',
            'md',
            'me',
            'mf',
            'mg',
            'mh',
            'mil',
            'mk',
            'ml',
            'mm',
            'mn',
            'mo',
            'mobi',
            'mp',
            'mq',
            'mr',
            'ms',
            'mt',
            'mu',
            'museum',
            'mv',
            'mw',
            'mx',
            'my',
            'mz',
            'na',
            'name',
            'nc',
            'ne',
            'net',
            'nf',
            'ng',
            'ni',
            'nl',
            'no',
            'np',
            'nr',
            'nu',
            'nz',
            'om',
            'org',
            'pa',
            'pe',
            'pf',
            'pg',
            'ph',
            'pk',
            'pl',
            'pm',
            'pn',
            'pr',
            'pro',
            'ps',
            'pt',
            'pw',
            'py',
            'qa',
            're',
            'ro',
            'rs',
            'ru',
            'rw',
            'sa',
            'sb',
            'sc',
            'sd',
            'se',
            'sg',
            'sh',
            'si',
            'sj',
            'sk',
            'sl',
            'sm',
            'sn',
            'so',
            'sr',
            'ss',
            'st',
            'su',
            'sv',
            'sx',
            'sy',
            'sz',
            'tc',
            'td',
            'tel',
            'tf',
            'tg',
            'th',
            'tj',
            'tk',
            'tl',
            'tm',
            'tn',
            'to',
            'tp',
            'tr',
            'travel',
            'tt',
            'tv',
            'tw',
            'tz',
            'ua',
            'ug',
            'uk',
            'um',
            'us',
            'uy',
            'uz',
            'va',
            'vc',
            've',
            'vg',
            'vi',
            'vn',
            'vu',
            'wf',
            'ws',
            'xxx',
            'ye',
            'yt',
            'za',
            'zm',
            'zw',
        ];
        $sub_domain = explode('.', $domain);
        $top_domain = '';
        $top_domain_count = 0;
        for ($i = count($sub_domain) - 1; $i >= 0; $i--) {
            if ($i == 0) {
                // just in case of something like NAME.COM
                break;
            }
            if (in_array($sub_domain [$i], $iana_root)) {
                $top_domain_count++;
                $top_domain = '.'.$sub_domain [$i].$top_domain;
                if ($top_domain_count >= 2) {
                    break;
                }
            }
        }
        $top_domain = $sub_domain [count($sub_domain) - $top_domain_count - 1].$top_domain;

        return $top_domain;
    }

    // 获取childrenIds
    public function getChildrenIds($categoryItem, &$childrenIdArr)
    {
        // dd($categoryItem);
        if (key_exists('children', $categoryItem)) {
            $childrenArr = $categoryItem['children'];
            // dd($childrenArr);
            foreach ($childrenArr as $children) {
                $childrenIdArr[] = $children['value'];
                $this->getChildrenIds($children, $childrenIdArr);
            }
        }
        // dd($childrenIdArr);
    }

    // 过滤词规则
    public function stopWords($text)
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
