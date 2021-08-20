<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Member;

use App\Helpers\DateHelper;
use App\Http\Center\Helper\PluginRpcHelper;
use App\Http\Fresns\FresnsApi\Base\FresnsBaseApiController;
use Illuminate\Http\Request;
use App\Http\Share\Common\ValidateService;
use App\Http\Fresns\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\Fresns\FresnsCommentAppends\FresnsCommentAppends;
use App\Http\Fresns\FresnsComments\FresnsComments;
use App\Http\Fresns\FresnsGroups\FresnsGroups;
use App\Http\Fresns\FresnsGroups\FresnsGroupsConfig;
use App\Http\Fresns\FresnsHashtags\FresnsHashtags;
use App\Http\Fresns\FresnsMemberFollows\FresnsMemberFollows;
use App\Http\Fresns\FresnsMemberFollows\FresnsMemberFollowsService;
use App\Http\Fresns\FresnsMemberLikes\FresnsMemberLikesService;
use App\Http\Fresns\FresnsMemberShields\FresnsMemberShieldsService;
use App\Http\Fresns\FresnsMemberStats\FresnsMemberStatsConfig;
use App\Http\Fresns\FresnsNotifies\FresnsNotifiesService;
use App\Http\Fresns\FresnsPostAppends\FresnsPostAppends;
use App\Http\Fresns\FresnsPosts\FresnsPosts;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Fresns\FresnsGroups\FresnsGroupsService;
use App\Http\Fresns\FresnsApi\Content\FresnsGroupResource;
use App\Http\Fresns\FresnsHashtags\FresnsHashtagsService;
use App\Http\Fresns\FresnsApi\Content\FresnsHashtagsResource;
use App\Http\Fresns\FresnsPosts\FresnsPostsService;
use App\Http\Fresns\FresnsApi\Content\Resource\FresnsMarkPostResource;
use App\Http\Fresns\FresnsComments\FresnsCommentsService;
use App\Http\Fresns\FresnsApi\Content\Resource\CommentMarkResource;
use App\Http\Fresns\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\Fresns\FresnsCmds\FresnsPlugin;
use App\Http\Fresns\FresnsCmds\FresnsPluginConfig;
use App\Http\Fresns\FresnsConfigs\FresnsConfigs;
use App\Http\Fresns\FresnsConfigs\FresnsConfigsConfig;
use App\Http\Fresns\FresnsConfigs\FresnsConfigsService;
use App\Http\Fresns\FresnsFileLogs\FresnsFileLogsConfig;
use App\Http\Fresns\FresnsMemberLikes\FresnsMemberLikes;
use App\Http\Fresns\FresnsMemberRoles\FresnsMemberRolesService;
use App\Http\Fresns\FresnsMembers\FresnsMembers;
use App\Http\Fresns\FresnsMemberStats\FresnsMemberStats;
use App\Http\Fresns\FresnsPostMembers\FresnsPostMembersConfig;
use App\Http\Fresns\FresnsSessionLogs\FresnsSessionLogs;
use App\Http\Fresns\FresnsSessionLogs\FresnsSessionLogsConfig;
use App\Http\Fresns\FresnsSessionLogs\FresnsSessionLogsService;
use App\Http\Fresns\FresnsStopWords\FresnsStopWords;
use App\Http\Share\AmGlobal\GlobalService;
use App\Http\Share\Common\ErrorCodeService;

class AmControllerApi extends FresnsBaseApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->service = new AmService();
        $this->initData();
    }

    public function auth(Request $request)
    {
        // 校验参数
        $rule = [
            'mid' => 'required|numeric',
        ];
        ValidateService::validateRule($request, $rule);

        $uid = GlobalService::getGlobalKey('user_id');

        $token = $request->header('token');


        $platform = $request->header('platform');
        $request->offsetSet('platform', $platform);
        $passwordBase64 = $request->input('password');

        if($passwordBase64){
            $password = base64_decode($passwordBase64,true);
            if($password == false){
                $password = $passwordBase64;
            }
        } else {
            $password = null;
        }


        $mid = $request->input('mid');
        $mid = FresnsMembers::where('uuid', $mid)->value('id');

        $checkMember = AmChecker::checkUserMember($mid, $uid);
        if ($checkMember == false) {
            $this->error(ErrorCodeService::CODE_FAIL);
        }

        $sessionLogId = GlobalService::getGlobalSessionKey('session_log_id');
        if($sessionLogId){
            $sessionInput = [
                'object_order_id' => $mid,
                'user_id' => $uid,
                'member_id' => $mid,
            ];
            FresnsSessionLogs::where('id',$sessionLogId)->update($sessionInput);
        }

        //查询该邮箱或手机号所属用户，近 1 小时内登录密码错误次数，达到 5 次，则限制登录。
        //session_logs 3-登陆 情况
        $startTime = date('Y-m-d H:i:s',strtotime("-1 hour"));
        $sessionCount = FresnsSessionLogs::where('created_at','>=',$startTime)
        ->where('user_id',$uid)
        ->where('member_id',$mid)
        ->where('object_result',FresnsSessionLogsConfig::OBJECT_RESULT_ERROR)
        ->where('object_type',FresnsSessionLogsConfig::OBJECT_TYPE_MEMBER_LOGIN)
        ->count();

        if($sessionCount >= 5){
            $this->error(ErrorCodeService::LOGIN_ERROR);
        }
        
        $member = FresnsMembers::where('id', $mid)->first();
        if (!empty($member['password'])) {
            if (!Hash::check($password, $member['password'])) {
                $this->error(ErrorCodeService::PASSWORD_INVALID);
            }
        }
        $langTag = ApiLanguageHelper::getLangTagByHeader();

        $request->offsetSet('langTag', $langTag);
        $request->offsetSet('mid', $mid);


        $data = $this->service->getMemberDetail($mid, $mid, true, $langTag);
        if ($data) {
            
            $cmd = FresnsPluginConfig::PLG_CMD_CREATE_SESSION_TOKEN;
            $input['uid'] = $request->header('uid');
            $input['platform'] = $request->header('platform');
            $input['mid'] = $member['uuid'];

            $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
            if (PluginRpcHelper::isErrorPluginResp($resp)) {
                $this->errorCheckInfo($resp);
            }
            $token = $resp['output']['token'] ?? '';
            $data['token'] = $token;
            $data['tokenExpiredTime'] = '';
        }

        $sessionId = GlobalService::getGlobalSessionKey('session_log_id');
        if ($sessionId) {
            FresnsSessionLogsService::updateSessionLogs($sessionId, 2, $uid, $mid, $mid);
        }

        $this->success($data);

    }

    public function memberEdit(Request $request)
    {
        // 校验参数
        $rule = [
            'gender' => 'numeric|in:0,1,2',
            'dialogLimit' => 'numeric',

        ];
        ValidateService::validateRule($request, $rule);

        $uid = GlobalService::getGlobalKey('user_id');
        $mid = GlobalService::getGlobalKey('member_id');

        $checkMember = AmChecker::checkUserMember($mid, $uid);
        if ($checkMember == false) {
            $this->error(ErrorCodeService::CODE_FAIL);
        }

        $mname = $request->input('mname');
        $nickname = $request->input('nickname');
        $bio = $request->input('bio');
        $member = FresnsMembers::where('id', $mid)->first();
        if (empty($member)) {
            $this->error(ErrorCodeService::NO_RECORD);
        }
        $last_name_at = $member['last_name_at'];
        $last_nickname_at = $member['last_nickname_at'];
        if ($mname) {
            $itemValue = FresnsConfigs::where('item_key', FresnsConfigsConfig::MNAME_EDIT)->value('item_value');
            if ($itemValue > 0) {
                if ($last_name_at) {
                    $begin_date = strtotime($last_name_at);
                    $end_date = strtotime(date('Y-m-d', time()));
                    $days = round(($end_date - $begin_date) / 3600 / 24);
                    // dd($itemValue);
                    if ($days <= $itemValue) {
                        $this->error(ErrorCodeService::UPDATE_TIME_ERROR);
                    }
                }
            }
            $disableNames = FresnsConfigs::where('item_key', 'disable_names')->value('item_value');
            $disableNamesArr = json_decode($disableNames, true);
            if (in_array($mname, $disableNamesArr)) {
                $this->error(ErrorCodeService::WXAPP_CONTENT_ERROR);
            }
            //判断名称是否重复
            $memberCount = FresnsMembers::where('name', $mname)->count();
            if ($memberCount > 0) {
                $this->error(ErrorCodeService::MEMBER_NAME_ERROR);
            }
        }

        if ($nickname) {
            $itemValue = FresnsConfigs::where('item_key', FresnsConfigsConfig::NICKNAME_EDIT)->value('item_value');
            if ($itemValue > 0) {
                if ($last_name_at) {
                    $begin_date = strtotime($last_nickname_at);
                    $end_date = strtotime(date('Y-m-d', time()));
                    $days = round(($end_date - $begin_date) / 3600 / 24);
                    if ($days <= $itemValue) {
                        $this->error(ErrorCodeService::UPDATE_TIME_ERROR);
                    }
                }
            }

            $stopWordsArr = FresnsStopWords::get()->toArray();

            foreach ($stopWordsArr as $v) {
                $str = strstr($nickname, $v['word']);
                if ($str != false) {
                    if ($v['member_mode'] == 2) {
                        $nickname = str_replace($v['word'], $v['replace_word'], $nickname);
                        $request->offsetSet('nickname', $nickname);
                    }
                    if ($v['member_mode'] == 3) {
                        $this->error(ErrorCodeService::UPDATE_TIME_ERROR);
                    }
                }
            }
        }

        if ($bio) {
            $stopWordsArr = FresnsStopWords::get()->toArray();

            foreach ($stopWordsArr as $v) {
                $str = strstr($bio, $v['word']);
                if ($str != false) {
                    if ($v['member_mode'] == 2) {
                        $bio = str_replace($v['word'], $v['replace_word'], $bio);
                        $request->offsetSet('bio', $bio);

                    }
                    if ($v['member_mode'] == 3) {
                        $this->error(ErrorCodeService::UPDATE_TIME_ERROR);
                    }
                }
            }
        }
        $map = AmConfig::MEMBER_EDIT;

        $itemArr = [];
        foreach ($map as $k => $v) {
            $req = $request->input($k);
            if ($req) {
                $itemArr[$v] = $req;
            }
        }

        if ($itemArr) {
            FresnsMembers::where('id', $mid)->update($itemArr);
        }

        if ($mname) {
            $input = [
                'last_name_at' => date('Y-m-d H:i:s', time())
            ];
            FresnsMembers::where('id', $mid)->update($input);
        }

        if ($nickname) {
            $input = [
                'last_nickname_at' => date('Y-m-d H:i:s', time())
            ];
            FresnsMembers::where('id', $mid)->update($input);
        }


        $sessionId = GlobalService::getGlobalSessionKey('session_log_id');
        if ($sessionId) {
            FresnsSessionLogsService::updateSessionLogs($sessionId, 2, $uid, $mid, $mid);
        }

        $this->success();
    }

    //获取用户角色
    public function memberRoles(Request $request)
    {
        // 校验参数
        $rule = [
            'type' => 'in:1,2,3',
            'pageSize' => 'numeric',
            'page' => 'numeric',
        ];

        ValidateService::validateRule($request, $rule);
        $page = $request->input('page',1);
        $pageSize = $request->input('pageSize',30);
        $fresnsMemberRolesService = new FresnsMemberRolesService();
        $request->offsetSet('currentPage', $page);
        $request->offsetSet('pageSize', $pageSize);
        $data = $fresnsMemberRolesService->searchData();
        $this->success($data);
    }

    public function memberMark(Request $request)
    {
        // 校验参数
        $rule = [
            'type' => 'required|numeric|in:1,2',
            'markType' => 'required|numeric|in:1,2,3',
            'markTarget' => 'required|numeric|in:1,2,3,4,5',
            'markId' => 'required',

        ];
        ValidateService::validateRule($request, $rule);

        $type = $request->input('type');
        $markType = $request->input('markType');
        $markTarget = $request->input('markTarget');
        $markId = $request->input('markId');
        $mid = GlobalService::getGlobalKey('member_id');
        //私有模式，成员已过期，不允许操作
        $siteMode = ApiConfigHelper::getConfigByItemKey('site_mode');
        if ($siteMode == 'private') {
            $midMember = FresnsMembers::where('id', $mid)->first();
            if (!empty($midMember['expired_at'])) {
                $time = date('Y-m-d H:i:s', time());
                if ($time > $midMember['expired_at']) {
                    $this->error(ErrorCodeService::USERS_NOT_AUTHORITY_ERROR);
                }
            }
        }

        //是否有权操作，根据配置表设置配置键名 > 运营配置 > 互动配置 > 互动行为设置，设置为 false 时，不可操作
        $checkerApi = AmChecker::checkMarkApi($markType, $markTarget);
        if ($checkerApi == false) {
            $this->error(ErrorCodeService::API_NO_CALL_ERROR);
        }
        //标记成员不可以是自己，以及自己发表的帖子、评论
        //成员
        if ($markTarget == 1) {
            $markId = FresnsMembers::where('uuid', $markId)->where('is_enable', 1)->value('id');
            if (empty($markId)) {
                $info = [
                    'markId' => 'null'
                ];
                $this->error(ErrorCodeService::NO_RECORD, $info);

            }
            if ($markId == $mid) {
                $this->error(ErrorCodeService::FOLLOW_ERROR);
            }
        }

        //小组关注专用：判断 groups > type_follow = 2 时，不能通过该接口建立关注。
        if ($markTarget == 2) {
            $groups = FresnsGroups::where('uuid', $markId)->where('is_enable', 1)->first();
            if (empty($groups)) {
                $info = [
                    'markId' => 'null'
                ];
                $this->error(ErrorCodeService::NO_RECORD, $info);

            }
            if ($groups['type_follow'] == 2) {
                $this->error(ErrorCodeService::GROUP_MARK_FOLLOW_TYPE_ERROR);
            }
            $markId = $groups['id'];
        }

        //话题
        if ($markTarget == 3) {
            $markId = FresnsHashtags::where('slug', $markId)->where('is_enable', 1)->value('id');
            if (empty($markId)) {
                $info = [
                    'markId' => 'null'
                ];
                $this->error(ErrorCodeService::NO_RECORD, $info);

            }
        }

        //帖子
        if ($markTarget == 4) {
            $posts = FresnsPosts::where('uuid', $markId)->where('is_enable', 1)->first();
            if (empty($posts)) {
                $info = [
                    'markId' => 'null'
                ];
                $this->error(ErrorCodeService::NO_RECORD, $info);

            }
            $memberId = $posts['member_id'];
            $markId = $posts['id'];
            if ($memberId == $mid) {
                $this->error(ErrorCodeService::FOLLOW_ERROR);
            }
        }
        //评论
        if ($markTarget == 5) {
            $comment = FresnsComments::where('uuid', $markId)->where('is_enable', 1)->first();
            if (empty($markId)) {
                $info = [
                    'markId' => 'null'
                ];
                $this->error(ErrorCodeService::NO_RECORD, $info);

            }
            $memberId = $comment['member_id'];
            $markId = $comment['id'];
            if ($memberId == $mid) {
                $this->error(ErrorCodeService::FOLLOW_ERROR);
            }
        }

        //校验是否重复操作
        switch ($type) {
            case 1:
                $checkMark = AmChecker::checkMark($markType, $markTarget, $mid, $markId);
                if ($checkMark === true) {
                    $this->error(ErrorCodeService::MEMBER_MARK_ERROR);
                }
                break;

            default:
                $checkMark = AmChecker::checkMark($markType, $markTarget, $mid, $markId);
                if ($checkMark === false) {
                    $this->error(ErrorCodeService::MEMBER_MARK_ERROR);
                }
                break;
        }

        switch ($markTarget) {
            case 1:
                switch ($markType) {
                    case 1:
                        if ($type == 1) {

                            FresnsMemberLikesService::addMemberLikes($mid, $markTarget, $markId, 'like_member_count',
                                'like_me_count');
                            //给对方录入一条通知
                            FresnsNotifiesService::markNotifies($markId, $mid, 3, $markTarget, '点赞');
                        } else {

                            FresnsMemberLikesService::delMemberLikes($mid, $markTarget, $markId);

                            DB::table(FresnsMemberStatsConfig::CFG_TABLE)->where('member_id',
                                $mid)->decrement('like_member_count');
                            DB::table(FresnsMemberStatsConfig::CFG_TABLE)->where('member_id',
                                $markId)->decrement('like_me_count');

                        }
                        break;
                    case 2:
                        $memberFollows = FresnsMemberFollows::where('follow_id', $mid)->where('member_id',
                            $markId)->first();

                        if ($type == 1) {
                            FresnsMemberFollowsService::addMemberFollow($mid, $markTarget, $markId);
                            if ($memberFollows) {
                                FresnsMemberFollows::where('id', $memberFollows['id'])->update(['is_mutual' => 1]);
                                FresnsMemberFollows::where('member_id', $mid)->where('follow_type',
                                    $markTarget)->where('follow_id', $markId)->update(['is_mutual' => 1]);
                            }

                            DB::table(FresnsMemberStatsConfig::CFG_TABLE)->where('member_id',
                                $mid)->increment('follow_member_count');
                            DB::table(FresnsMemberStatsConfig::CFG_TABLE)->where('member_id',
                                $markId)->increment('follow_me_count');
                            //给对方录入一条通知
                            FresnsNotifiesService::markNotifies($markId, $mid, 2, $markTarget, '关注');
                        } else {
                            FresnsMemberFollowsService::delMemberFollow($mid, $markTarget, $markId);
                            FresnsMemberFollows::where('member_id', $markId)->where('follow_type',
                                $markTarget)->where('follow_id', $mid)->update(['is_mutual' => 0]);
                            DB::table(FresnsMemberStatsConfig::CFG_TABLE)->where('member_id',
                                $mid)->decrement('follow_member_count');
                            DB::table(FresnsMemberStatsConfig::CFG_TABLE)->where('member_id',
                                $markId)->decrement('follow_me_count');

                        }
                        break;
                    default:
                        if ($type == 1) {
                            FresnsMemberShieldsService::addMemberShields($mid, $markTarget, $markId);
                            DB::table(FresnsMemberStatsConfig::CFG_TABLE)->where('member_id',
                                $mid)->increment('shield_member_count');
                            DB::table(FresnsMemberStatsConfig::CFG_TABLE)->where('member_id',
                                $markId)->increment('shield_me_count');
                        } else {
                            FresnsMemberShieldsService::delMemberShields($mid, $markTarget, $markId);
                            DB::table(FresnsMemberStatsConfig::CFG_TABLE)->where('member_id',
                                $mid)->decrement('shield_member_count');
                            DB::table(FresnsMemberStatsConfig::CFG_TABLE)->where('member_id',
                                $markId)->decrement('shield_me_count');

                        }
                        break;
                }

                break;
            case 2:
                switch ($markType) {
                    case 1:
                        if ($type == 1) {
                            FresnsMemberLikesService::addMemberLikes($mid, $markTarget, $markId);
                            FresnsGroups::where('id', $markId)->increment('like_count');
                        } else {
                            FresnsMemberLikesService::delMemberLikes($mid, $markTarget, $markId);
                            FresnsGroups::where('id', $markId)->decrement('like_count');
                        }
                        break;
                    case 2:
                        if ($type == 1) {
                            FresnsMemberFollowsService::addMemberFollow($mid, $markTarget, $markId);
                            FresnsGroups::where('id', $markId)->increment('follow_count');
                        } else {
                            FresnsMemberFollowsService::delMemberFollow($mid, $markTarget, $markId);
                            FresnsGroups::where('id', $markId)->decrement('follow_count');
                        }

                        break;
                    default:
                        if ($type == 1) {
                            FresnsMemberShieldsService::addMemberShields($mid, $markTarget, $markId);
                            DB::table(FresnsGroupsConfig::CFG_TABLE)->where('id', $markId)->increment('shield_count');
                        } else {
                            FresnsMemberShieldsService::delMemberShields($mid, $markTarget, $markId);
                            DB::table(FresnsGroupsConfig::CFG_TABLE)->where('id', $markId)->decrement('shield_count');
                        }
                        break;
                }
                break;
            case 3:
                switch ($markType) {
                    case 1:
                        if ($type == 1) {
                            FresnsMemberLikesService::addMemberLikes($mid, $markTarget, $markId);
                            FresnsHashtags::where('id', $markId)->increment('like_count');
                            //向配置表插入数据
                            FresnsConfigsService::addLikeCounts('hashtag_like_counts');
                        } else {
                            FresnsMemberLikesService::delMemberLikes($mid, $markTarget, $markId);
                            FresnsHashtags::where('id', $markId)->decrement('like_count');
                            FresnsConfigsService::delLikeCounts('hashtag_like_counts');
                        }
                        break;
                    case 2:
                        if ($type == 1) {
                            FresnsMemberFollowsService::addMemberFollow($mid, $markTarget, $markId);
                            FresnsHashtags::where('id', $markId)->increment('follow_count');
                            //向配置表插入数据
                            FresnsConfigsService::addLikeCounts('hashtag_follow_counts');
                        } else {
                            FresnsMemberFollowsService::delMemberFollow($mid, $markTarget, $markId);
                            FresnsHashtags::where('id', $markId)->decrement('follow_count');
                            FresnsConfigsService::delLikeCounts('hashtag_follow_counts');
                        }
                        break;
                    default:
                        if ($type == 1) {
                            FresnsMemberFollowsService::addMemberFollow($mid, $markTarget, $markId);
                            FresnsHashtags::where('id', $markId)->increment('shield_count');
                            //向配置表插入数据
                            FresnsConfigsService::addLikeCounts('hashtag_shield_counts');
                        } else {
                            FresnsMemberFollowsService::delMemberFollow($mid, $markTarget, $markId);
                            FresnsHashtags::where('id', $markId)->decrement('shield_count');
                            FresnsConfigsService::delLikeCounts('hashtag_shield_counts');
                        }
                        break;

                }

                break;
            case 4:
                switch ($markType) {
                    case 1:
                        if ($type == 1) {
                            FresnsMemberLikesService::addMemberLikes($mid, $markTarget, $markId);
                            FresnsPosts::where('id', $markId)->increment('like_count');
                            //向配置表插入数据
                            FresnsConfigsService::addLikeCounts('post_like_counts');
                            //插入一条通知
                            $post = FresnsPosts::where('id', $markId)->first();
                            FresnsNotifiesService::markNotifies($post['member_id'], $mid, 3, $markTarget,
                                $post['title'], 1, $markId);
                        } else {
                            FresnsMemberLikesService::delMemberLikes($mid, $markTarget, $markId);
                            FresnsPosts::where('id', $markId)->decrement('like_count');
                            FresnsConfigsService::delLikeCounts('post_like_counts');
                        }
                        break;
                    case 2:
                        if ($type == 1) {
                            FresnsMemberFollowsService::addMemberFollow($mid, $markTarget, $markId);
                            FresnsPosts::where('id', $markId)->increment('follow_count');
                            //向配置表插入数据
                            FresnsConfigsService::addLikeCounts('post_follow_counts');
                            //插入一条通知
                            $post = FresnsPosts::where('id', $markId)->first();
                            FresnsNotifiesService::markNotifies($post['member_id'], $mid, 2, $markTarget,
                                $post['title'], 1, $markId);
                        } else {
                            FresnsMemberFollowsService::delMemberFollow($mid, $markTarget, $markId);
                            FresnsPosts::where('id', $markId)->decrement('follow_count');
                            FresnsConfigsService::delLikeCounts('post_follow_counts');
                        }
                        break;
                    default:
                        if ($type == 1) {
                            FresnsMemberFollowsService::addMemberFollow($mid, $markTarget, $markId);
                            FresnsPosts::where('id', $markId)->increment('shield_count');
                            //向配置表插入数据
                            FresnsConfigsService::addLikeCounts('post_shield_counts');
                        } else {
                            FresnsMemberFollowsService::delMemberFollow($mid, $markTarget, $markId);
                            FresnsPosts::where('id', $markId)->decrement('shield_count');
                            FresnsConfigsService::delLikeCounts('post_shield_counts');
                        }
                        break;
                }
                break;
            default:
                $comment = FresnsComments::where('id', $markId)->first();

                switch ($markType) {
                    case 1:
                        if ($type == 1) {
                            FresnsMemberLikesService::addMemberLikes($mid, $markTarget, $markId);
                            FresnsComments::where('id', $markId)->increment('like_count');
                            FresnsPosts::where('id', $comment['post_id'])->increment('comment_like_count');
                            //向配置表插入数据
                            FresnsConfigsService::addLikeCounts('comment_like_counts');
                            if ($comment['parent_id'] > 0) {
                                FresnsComments::where('id', $comment['parent_id'])->increment('comment_like_count');
                            }
                            //插入一条通知
                            FresnsNotifiesService::markNotifies($comment['member_id'], $mid, 3, $markTarget,
                                $comment['content'], 2, $markId);
                        } else {
                            FresnsMemberLikesService::delMemberLikes($mid, $markTarget, $markId);
                            FresnsComments::where('id', $markId)->decrement('like_count');
                            FresnsPosts::where('id', $comment['post_id'])->decrement('comment_like_count');
                            //向配置表插入数据
                            FresnsConfigsService::delLikeCounts('comment_like_counts');
                            if ($comment['parent_id'] > 0) {
                                FresnsComments::where('id', $comment['parent_id'])->decrement('comment_like_count');
                            }
                        }
                        break;
                    case 2:
                        if ($type == 1) {
                            FresnsMemberLikesService::addMemberLikes($mid, $markTarget, $markId);
                            FresnsComments::where('id', $markId)->increment('follow_count');
                            //向配置表插入数据
                            FresnsConfigsService::addLikeCounts('comment_follow_counts');
                            //插入一条通知
                            FresnsNotifiesService::markNotifies($comment['member_id'], $mid, 2, $markTarget,
                                $comment['content'], 2, $markId);
                        } else {
                            FresnsMemberLikesService::delMemberLikes($mid, $markTarget, $markId);
                            FresnsComments::where('id', $markId)->decrement('follow_count');
                            //向配置表插入数据
                            FresnsConfigsService::delLikeCounts('comment_follow_counts');
                        }
                        break;
                    default:
                        if ($type == 1) {
                            FresnsMemberLikesService::addMemberLikes($mid, $markTarget, $markId);
                            FresnsComments::where('id', $markId)->increment('shield_count');
                            //向配置表插入数据
                            FresnsConfigsService::addLikeCounts('comment_shield_counts');
                        } else {
                            FresnsMemberLikesService::delMemberLikes($mid, $markTarget, $markId);
                            FresnsComments::where('id', $markId)->decrement('shield_count');
                            //向配置表插入数据
                            FresnsConfigsService::delLikeCounts('comment_shield_counts');
                        }
                        break;
                }
                break;
        }

        $this->success();
    }

    /**
     * 成员操作删除内容
     * 删除都要验证帖子或者评论的作者是否为本人
     * 需要验证成员是否有权删除，有一些内容可能不被允许删除，查询附属信息表 can_delete 字段。
     */
    public function memberDelete(Request $request)
    {
        // 校验参数
        $rule = [
            'type' => 'required|numeric|in:1,2',
            'uuid' => 'required',

        ];
        ValidateService::validateRule($request, $rule);

        $mid = GlobalService::getGlobalKey('member_id');

        $uuid = $request->input('uuid');
        $type = $request->input('type');
        switch ($type) {
            case 1:
                //校验
                $posts = FresnsPosts::where('uuid', $uuid)->first();
                if (empty($posts)) {
                    $this->error(ErrorCodeService::DELETE_FILE_ERROR);
                }
                if ($posts['member_id'] != $mid) {
                    $this->error(ErrorCodeService::NO_PERMISSION);
                }
                $postsAppend = FresnsPostAppends::where('post_id', $posts['id'])->first();
                if ($postsAppend['can_delete'] == 0) {
                    $this->error(ErrorCodeService::NO_PERMISSION);
                }
                FresnsPosts::where('id', $posts['id'])->delete();
                FresnsPostAppends::where('id', $postsAppend['id'])->delete();
                break;

            default:
                $comments = FresnsComments::where('uuid', $uuid)->first();

                if (empty($comments)) {
                    $this->error(ErrorCodeService::DELETE_COMMENT_ERROR);
                }
                if ($comments['member_id'] != $mid) {
                    $this->error(ErrorCodeService::NO_PERMISSION);
                }
                $commentsAppend = FresnsCommentAppends::where('comment_id', $comments['id'])->first();
                if (!empty($commentsAppend)) {
                    if ($commentsAppend['can_delete'] == 0) {
                        $this->error(ErrorCodeService::NO_PERMISSION);
                    }
                }

                FresnsComments::where('id', $comments['id'])->delete();
                FresnsMemberStats::where('member_id', $mid)->decrement('comment_publish_count');
                FresnsConfigs::where('item_key', 'comment_counts')->decrement('item_value');
                if (!empty($commentsAppend['id'])) {
                    FresnsCommentAppends::where('id', $commentsAppend['id'])->delete();
                }
                break;
        }

        $this->success();
    }

    /**
     * 自己的 mid 和接口 viewMid 参数一样，则代表自己查看自己的信息；参数不一样，代表查看别人的信息。
     * 查看别人的信息，参数 extcredits1 要判断 extcredits1_status 键值，未启用或者为私有状态，则不输出。其他 2～5 同理。
     * 查看别人的信息，featureExpands 和 dataExpands 扩展列表不输出。
     * 查看自己的信息，manages 扩展列表不输出。
     */
    public function memberDetail(Request $request)
    {
        $mid = GlobalService::getGlobalKey('member_id');
        $viewMid = $request->input('viewMid');
        $viewMname = $request->input('viewMname');
        $langTag = ApiLanguageHelper::getLangTagByHeader();

        $request->offsetSet('langTag', $langTag);
        $request->offsetSet('mid', $mid);
        if (empty($viewMid)) {
            $viewMid = FresnsMembers::where('name', $viewMname)->value('id');
        } else {
            $viewMid = FresnsMembers::where('uuid', $viewMid)->value('id');
        }

        if (empty($viewMid)) {
            $this->error(ErrorCodeService::UID_EXIST_ERROR);
        }

        //是否是本人
        $isMe = false;
        if ($mid == $viewMid) {
            $isMe = true;
        }

        $data['common'] = $this->service->common($viewMid, $langTag, $isMe,$mid);
        $data['detail'] = $this->service->getMemberDetail($mid, $viewMid, $isMe, $langTag);

        $this->success($data);
    }

    public function memberLists(Request $request)
    {
        // 校验参数
        $rule = [
            'gender' => 'numeric|in:0,1,2',
            'sortDirection' => 'numeric|in:1,2',
            'pageSize' => 'numeric',
            'page' => 'numeric',
        ];
        ValidateService::validateRule($request, $rule);

        $searchKey = $request->input('searchKey');
        $gender = $request->input('gender', 3);
        $sortType = $request->input('sortType', 'follow');
        $createdTimeGt = $request->input('createdTimeGt');
        $createdTimeLt = $request->input('createdTimeLt');
        $sortDirection = $request->input('sortDirection', 2);
        $pageSize = $request->input('pageSize', 20);
        $page = $request->input('page', 1);
        if ($pageSize > 50) {
            $pageSize = 50;
        }
        $query = DB::table('members as me');
        $query = $query->select('me.*')->leftJoin('member_stats as st', 'me.id', '=', 'st.member_id');

        if ($searchKey) {
            $memberIdArr1 = FresnsMembers::where('name', 'LIKE', "%$searchKey%")->pluck('id')->toArray();
            $memberIdArr2 = FresnsMembers::where('nickname', 'LIKE', "%$searchKey%")->pluck('id')->toArray();
            $idArr = array_unique(array_merge($memberIdArr1, $memberIdArr2));
            $query->whereIn('me.id', $idArr);
        }
        if ($createdTimeGt) {
            $createdTimeGt = DateHelper::timezoneToAsiaShanghai($createdTimeGt);
            $query->where('st.created_at', '>', $createdTimeGt);
        }
        if ($createdTimeLt) {
            $createdTimeLt = DateHelper::timezoneToAsiaShanghai($createdTimeLt);
            $query->where('st.created_at', '<', $createdTimeLt);
        }

        if (in_array($gender, [0, 1, 2])) {

            $query->where('me.gender', $gender);
        }

        $sortDirection = $sortDirection == 1 ? 'ASC' : 'DESC';
        switch ($sortType) {
            case 'like':
                $query->orderBy('st.like_me_count', $sortDirection);
                break;
            case 'follow':
                $query->orderBy('st.follow_me_count', $sortDirection);
                break;
            case 'shield':
                $query->orderBy('st.shield_me_count', $sortDirection);
                break;
            case 'post':
                $query->orderBy('st.post_publish_count', $sortDirection);
                break;
            case 'comment':
                $query->orderBy('st.comment_publish_count', $sortDirection);
                break;
            case 'postLike':
                $query->orderBy('st.post_like_count', $sortDirection);
                break;
            case 'commentLike':
                $query->orderBy('st.comment_like_count', $sortDirection);
                break;
            case 'extcredits1':
                $query->orderBy('st.extcredits1', $sortDirection);
                break;
            case 'extcredits2':
                $query->orderBy('st.extcredits2', $sortDirection);
                break;
            case 'extcredits3':
                $query->orderBy('st.extcredits3', $sortDirection);
                break;
            case 'extcredits4':
                $query->orderBy('st.extcredits4', $sortDirection);
                break;
            default:
                $query->orderBy('st.extcredits5', $sortDirection);
                break;
        }

        $item = $query->paginate($pageSize, ['*'], 'page', $page);
        // dd($createdTimeGt);
        $data = [];
        $data['list'] = FresnsMemberListsResource::collection($item->items())->toArray($item->items());
        $pagination['total'] = $item->total();
        $pagination['current'] = $page;
        $pagination['pageSize'] = $pageSize;
        $pagination['lastPage'] = $item->lastPage();

        $data['pagination'] = $pagination;
        $this->success($data);
    }

    //获取互动成员列表
    public function memberInteractions(Request $request)
    {
        // 校验参数
        $rule = [
            'type' => 'required|in:1,2,3,4,5',
            'objectType' => 'numeric|in:1,2,3,4,5',
            'objectId' => 'required',
            'sortDirection' => 'numeric',
            'pageSize' => 'numeric',
            'page' => 'numeric',
        ];
        ValidateService::validateRule($request, $rule);

        $type = $request->input('type');
        $objectType = $request->input('objectType', 1);
        $objectId = $request->input('objectId');
        $sortDirection = $request->input('sortDirection');
        $pageSize = $request->input('pageSize', 30);
        $page = $request->input('page', 1);

        //查看别人信息时，是否输出数据，根据配置表设置配置键名 > 运营配置 > 互动配置 > 查看别人内容设置，设置为 false 时，不输出数据。
        $typeArr = [4, 5];
        if (!in_array($type, $typeArr)) {
            $isMarkLists = AmChecker::checkMarkLists($type, $objectType);
            if ($isMarkLists == false) {
                $this->error(ErrorCodeService::USERS_NOT_AUTHORITY_ERROR);
            }
        }


        $idArr = [];
        //type=1 获得点赞了 objectType > objectId 的所有成员列表。查询 member_likes 表。
        //type=2 获得关注了 objectType > objectId 的所有成员列表。查询 member_follows 表。
        //type=3 获得屏蔽了 objectType > objectId 的所有成员列表。查询 member_shields 表。
        //需要判断「互动配置」是否允许请求。
        //是否输出数据，根据配置表设置配置键名 > 运营配置 > 互动配置 > 查看别人内容设置，设置为 false 时，不输出数据
        switch ($type) {
            case 1:
                $likeId = 0;
                switch ($objectType) {
                    case 1:
                        $it_like_members = ApiConfigHelper::getConfigByItemKey('it_like_members');
                        if ($it_like_members == true) {
                            $likeId = FresnsMembers::where('uuid', $objectId)->value('id');
                        }
                        break;
                    case 2:
                        $it_like_groups = ApiConfigHelper::getConfigByItemKey('it_like_groups');
                        if ($it_like_groups == true) {
                            $likeId = FresnsGroups::where('uuid', $objectId)->value('id');
                        }
                        break;
                    case 3:
                        $it_like_hashtags = ApiConfigHelper::getConfigByItemKey('it_like_hashtags');
                        if ($it_like_hashtags == true) {
                            $likeId = $objectId;
                        }

                        break;
                    case 4:
                        $it_like_posts = ApiConfigHelper::getConfigByItemKey('it_like_posts');
                        if ($it_like_posts == true) {
                            $likeId = FresnsPosts::where('uuid', $objectId)->value('id');
                        }
                        break;
                    default:
                        $it_like_comments = ApiConfigHelper::getConfigByItemKey('it_like_comments');
                        if ($it_like_comments == true) {
                            $likeId = FresnsComments::where('uuid', $objectId)->value('id');
                        }
                        break;
                }
                $idArr = FresnsMemberLikes::where('like_type', $objectType)->where('like_id',
                    $likeId)->pluck('member_id')->toArray();
                break;
            case 2:
                $followId = 0;
                switch ($objectType) {
                    case 1:
                        $it_follow_members = ApiConfigHelper::getConfigByItemKey('it_follow_members');
                        if ($it_follow_members == true) {
                            $followId = FresnsMembers::where('uuid', $objectId)->value('id');
                        }
                        break;
                    case 2:
                        $it_follow_groups = ApiConfigHelper::getConfigByItemKey('it_follow_groups');
                        if ($it_follow_groups == true) {
                            $followId = FresnsGroups::where('uuid', $objectId)->value('id');
                        }
                        break;
                    case 3:
                        $it_follow_hashtags = ApiConfigHelper::getConfigByItemKey('it_follow_hashtags');
                        if ($it_follow_hashtags == true) {
                            $followId = $objectId;
                        }
                        break;
                    case 4:
                        $it_follow_posts = ApiConfigHelper::getConfigByItemKey('it_follow_posts');
                        if ($it_follow_posts == true) {
                            $followId = FresnsPosts::where('uuid', $objectId)->value('id');
                        }
                        break;
                    default:
                        $it_follow_comments = ApiConfigHelper::getConfigByItemKey('it_follow_comments');
                        if ($it_follow_comments == true) {
                            $followId = FresnsComments::where('uuid', $objectId)->value('id');
                        }
                        break;
                }
                $idArr = FresnsMemberFollows::where('follow_type', $objectType)->where('follow_id',
                    $followId)->pluck('member_id')->toArray();

                break;
            case 3:
                $shieldId = 0;
                switch ($objectType) {
                    case 1:
                        $it_shield_members = ApiConfigHelper::getConfigByItemKey('it_shield_members');
                        if ($it_shield_members == true) {
                            $shieldId = FresnsMembers::where('uuid', $objectId)->value('id');

                        }
                        break;
                    case 2:
                        $it_shield_groups = ApiConfigHelper::getConfigByItemKey('it_shield_groups');
                        if ($it_shield_groups == true) {
                            $shieldId = FresnsGroups::where('uuid', $objectId)->value('id');

                        }
                        break;
                    case 3:
                        $it_shield_hashtags = ApiConfigHelper::getConfigByItemKey('it_shield_hashtags');
                        if ($it_shield_hashtags == true) {
                            $shieldId = $objectId;
                        }
                        break;
                    case 4:
                        $it_shield_posts = ApiConfigHelper::getConfigByItemKey('it_shield_posts');
                        if ($it_shield_posts == true) {
                            $shieldId = FresnsPosts::where('uuid', $objectId)->value('id');
                        }
                        break;
                    default:
                        $it_shield_comments = ApiConfigHelper::getConfigByItemKey('it_shield_comments');
                        if ($it_shield_comments == true) {
                            $shieldId = FresnsComments::where('uuid', $objectId)->value('id');
                        }
                        break;
                }
                $idArr = FresnsMemberFollows::where('follow_type', $objectType)->where('follow_id',
                    $shieldId)->pluck('member_id')->toArray();

                break;
            case 4:
                if ($objectType == 4) {
                    $postUuidArr = FresnsPosts::where('uuid', $objectId)->pluck('id')->toArray();
                    $idArr = DB::table(FresnsPostMembersConfig::CFG_TABLE)->whereIn('post_id',
                        $postUuidArr)->pluck('member_id')->toArray();
                }
                break;
            default:
                $idArr = DB::table(FresnsFileLogsConfig::CFG_TABLE)->where('file_id',
                    $objectId)->pluck('member_id')->toArray();

                break;
        }

        $query = DB::table('members as me');
        $query = $query->select('me.*')->leftJoin('member_stats as st', 'me.id', '=', 'st.member_id');

        $query->whereIn('me.id', $idArr);

        $sortDirection = $sortDirection == 1 ? 'ASC' : 'DESC';
        $query->orderBy('me.created_at', $sortDirection);


        $item = $query->paginate($pageSize, ['*'], 'page', $page);

        $data = [];
        $data['list'] = FresnsMemberListsResource::collection($item->items())->toArray($item->items());
        $pagination['total'] = $item->total();
        $pagination['current'] = $page;
        $pagination['pageSize'] = $pageSize;
        $pagination['lastPage'] = $item->lastPage();

        $data['pagination'] = $pagination;
        $this->success($data);

    }

    public function memberMarkLists(Request $request)
    {
        // 校验参数
        $rule = [
            // 'viewMid'    => 'required',
            'viewType' => 'required|numeric|in:1,2,3',
            'viewTarget' => 'required|numeric|in:1,2,3,4,5',
            'pageSize' => 'numeric',
            'page' => 'numeric',
        ];
        ValidateService::validateRule($request, $rule);

        $viewTarget = $request->input('viewTarget');
        $pageSize = $request->input('pageSize', 30);
        $page = $request->input('page', 1);
        $viewMid = $request->input('viewMid');
        $viewMname = $request->input('viewMname');
        $viewType = $request->input('viewType');

        $data = [];
        if (empty($viewMid) && empty($viewMname)) {
            $info = [
                'null body' => 'viewMid或viewMname为空',
            ];
            $this->error(ErrorCodeService::CODE_PARAM_ERROR, $info);
        }
        if (empty($viewMid)) {
            $mid = FresnsMembers::where('name', $viewMname)->value('id');
        } else {
            $mid = FresnsMembers::where('uuid', $viewMid)->value('id');
        }

        if (empty($mid)) {
            $info = [
                'null member' => 'viewMid或viewMname',
            ];
            $this->error(ErrorCodeService::NO_RECORD, $info);
        }

        $authMemberId = GlobalService::getGlobalKey('member_id');
        //查看别人信息时，是否输出数据，根据配置表设置配置键名 > 运营配置 > 互动配置 > 查看别人内容设置，设置为 false 时，不输出数据。
        if ($mid != $authMemberId) {
            $isMarkLists = AmChecker::checkMarkLists($viewType, $viewTarget);
            if ($isMarkLists == false) {
                $this->error(ErrorCodeService::USERS_NOT_AUTHORITY_ERROR);
            }
        }

        $request->offsetSet('viewMid', $mid);

        switch ($viewTarget) {
            case 1:
                $itLikeMembers = ApiConfigHelper::getConfigByItemKey('it_like_members');
                $data = AmService::getMemberList($request);
                if ($itLikeMembers == false) {
                    $data['list'] = [];
                    $data['pagination']['current'] = 1;
                    $data['pagination']['lastPage'] = 1;
                    $data['pagination']['total'] = 0;
                }
                break;
            case 2:
                $groupArr = AmService::getGroupList($request);
                $groupIds = implode(',', $groupArr);

                $FresnsDialogsService = new FresnsGroupsService();
                $request->offsetSet('ids', $groupIds);
                $request->offsetSet('currentPage', $page);
                $request->offsetSet('pageSize', $pageSize);
                $FresnsDialogsService->setResource(FresnsGroupResource::class);
                $data = $FresnsDialogsService->searchData();

                break;
            case 3:
                $hashtagArr = AmService::getHashtagList($request);
                $hashtagIds = implode(',', $hashtagArr);
                $FresnsHashtagsService = new FresnsHashtagsService();
                $request->offsetSet('ids', $hashtagIds);
                $request->offsetSet('currentPage', $page);
                $request->offsetSet('pageSize', $pageSize);
                $FresnsHashtagsService->setResource(FresnsHashtagsResource::class);
                $data = $FresnsHashtagsService->searchData();
                break;
            case 4:
                $postArr = AmService::getPostList($request);
                $postIds = implode(',', $postArr);
                $FresnsPostsService = new FresnsPostsService();
                $request->offsetSet('ids', $postIds);
                $request->offsetSet('currentPage', $page);
                $request->offsetSet('pageSize', $pageSize);
                $FresnsPostsService->setResource(FresnsMarkPostResource::class);
                $data = $FresnsPostsService->searchData();
                break;
            default:
                $commentArr = AmService::getPostList($request);
                $commentIds = implode(',', $commentArr);
                $FresnsCommentsService = new FresnsCommentsService();
                $request->offsetSet('ids', $commentIds);
                $request->offsetSet('currentPage', $page);
                $request->offsetSet('pageSize', $pageSize);
                $FresnsCommentsService->setResource(CommentMarkResource::class);
                $data = $FresnsCommentsService->searchData();
                break;
        }

        $this->success($data);
    }

}

