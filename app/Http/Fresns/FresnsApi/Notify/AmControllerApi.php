<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Notify;

use App\Http\Share\Common\ValidateService;
use App\Http\Fresns\FresnsApi\Base\FresnsBaseApiController;
use App\Http\Fresns\FresnsDialogMessages\FresnsDialogMessages;
use App\Http\Fresns\FresnsDialogMessages\FresnsDialogMessagesService;
use App\Http\Fresns\FresnsDialogs\FresnsDialogs;
use App\Http\Fresns\FresnsDialogs\FresnsDialogsConfig;
use App\Http\Fresns\FresnsDialogs\FresnsDialogsService;
use App\Http\Fresns\FresnsNotifies\FresnsNotifies;
use App\Http\Fresns\FresnsNotifies\FresnsNotifiesConfig;
use App\Http\Fresns\FresnsNotifies\FresnsNotifiesService;
use App\Http\Fresns\FresnsMembers\FresnsMembersConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Fresns\FresnsDialogMessages\FresnsDialogMessagesConfig;
use App\Http\Fresns\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\Fresns\FresnsApi\Content\AmConfig as ContentConfig;
use App\Http\Share\Common\ErrorCodeService;
use App\Http\Share\AmGlobal\GlobalService;
use App\Http\Fresns\FresnsMemberRoles\FresnsMemberRoles;
use App\Http\Fresns\FresnsMembers\FresnsMembers;
use App\Http\Fresns\FresnsMemberRoleRels\FresnsMemberRoleRels;
use App\Http\Fresns\FresnsMemberFollows\FresnsMemberFollows;
use App\Http\Fresns\FresnsFiles\FresnsFiles;
use App\Http\Fresns\FresnsApi\Helpers\ApiCommonHelper;
use App\Http\Fresns\FresnsSessionLogs\FresnsSessionLogsService;
use App\Http\Fresns\FresnsSessionLogs\FresnsSessionLogs;
use App\Http\Fresns\FresnsApi\Helpers\ApiFileHelper;

class AmControllerApi extends FresnsBaseApiController
{
    // public function __construct()
    // {
    //     $this->initData();
    // }
    // 获取未读数
    public function unread(Request $request)
    {
        $member_id = GlobalService::getGlobalKey('member_id');
        //    dd($member_id);
        // 系统
        $system_count = FresnsNotifies::where('member_id', $member_id)->where('source_type',
            AmConfig::SOURCE_TYPE_1)->where('status', AmConfig::NO_READ)->count();
        // 关注
        $follow_count = FresnsNotifies::where('member_id', $member_id)->where('source_type',
            AmConfig::SOURCE_TYPE_2)->where('status', AmConfig::NO_READ)->count();
        // 点赞
        $like_count = FresnsNotifies::where('member_id', $member_id)->where('source_type',
            AmConfig::SOURCE_TYPE_3)->where('status', AmConfig::NO_READ)->count();
        // 评论
        $comment_count = FresnsNotifies::where('member_id', $member_id)->where('source_type',
            AmConfig::SOURCE_TYPE_4)->where('status', AmConfig::NO_READ)->count();
        // 提及（艾特）
        $mention_count = FresnsNotifies::where('member_id', $member_id)->where('source_type',
            AmConfig::SOURCE_TYPE_5)->where('status', AmConfig::NO_READ)->count();
        // 推荐
        $recommend_count = FresnsNotifies::where('member_id', $member_id)->where('source_type',
            AmConfig::SOURCE_TYPE_6)->where('status', AmConfig::NO_READ)->count();

        $data = [
            'system' => $system_count,
            'follow' => $follow_count,
            'like' => $like_count,
            'comment' => $comment_count,
            'mention' => $mention_count,
            'recommend' => $recommend_count,
        ];
        $this->success($data);
    }

    // 获取消息列表
    public function lists(Request $request)
    {
        $rule = [
            'type' => 'required|in:1,2,3,4,5,6',
        ];
        ValidateService::validateRule($request, $rule);
        $uid = $this->uid;
        $member_id = $this->mid;
        $uid = $this->uid;
        if (empty($uid)) {
            $this->error(ErrorCodeService::USER_REQUIRED_ERROR);
        }
        if (empty($member_id)) {
            $this->error(ErrorCodeService::MEMBER_REQUIRED_ERROR);
        }
        $type = $request->input('type');
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 30);
        $uid = $this->uid;
        $member_id = GlobalService::getGlobalKey('member_id');
        // dd($member_id);
        $FresnsNotifiesService = new FresnsNotifiesService();
        $request->offsetSet('currentPage', $page);
        $request->offsetSet('pageSize', $pageSize);
        $request->offsetSet('member_id', $member_id);
        $FresnsNotifiesService->setResource(NotifyResource::class);
        $list = $FresnsNotifiesService->searchData();
        $data = [
            'pagination' => $list['pagination'],
            'detail' => $list['list'],
        ];
        $this->success($data);
    }

    // 更新阅读状态
    public function read(Request $request)
    {
        $rule = [
            'type' => 'required|in:1,2,3,4,5,6',
        ];
        ValidateService::validateRule($request, $rule);
        $uid = $this->uid;
        $member_id = $this->mid;
        $uid = $this->uid;
        if (empty($uid)) {
            $this->error(ErrorCodeService::USER_REQUIRED_ERROR);
        }
        if (empty($member_id)) {
            $this->error(ErrorCodeService::MEMBER_REQUIRED_ERROR);
        }
        $member_id = GlobalService::getGlobalKey('member_id');
        $type = $request->input('type');
        // 将该类型下我收到的消息全部设置为已读。
        $system_count = FresnsNotifies::where('member_id', $member_id)->where('source_type',
            $type)->update(['status' => AmConfig::READED]);
        $this->success();
    }

    // 删除消息
    public function delete(Request $request)
    {
        $rule = [
            'messageId' => 'required|array',
        ];
        ValidateService::validateRule($request, $rule);
        $uid = $this->uid;
        $member_id = $this->mid;
        $uid = $this->uid;
        if (empty($uid)) {
            $this->error(ErrorCodeService::USER_REQUIRED_ERROR);
        }
        if (empty($member_id)) {
            $this->error(ErrorCodeService::MEMBER_REQUIRED_ERROR);
        }
        $member_id = GlobalService::getGlobalKey('member_id');
        $idArr = $request->input('messageId');
        $result = self::isExsitMember($idArr, FresnsNotifiesConfig::CFG_TABLE, 'member_id', $member_id);
        // dd($result);
        if (!$result) {
            $this->error(ErrorCodeService::DELETED_NOTIFY_ERROR);
        }
        FresnsNotifies::whereIn('id', $idArr)->delete();
        $this->success();
    }

    // 获取会话列表
    public function dialog_lists(Request $request)
    {
        $uid = $this->uid;
        $member_id = $this->mid;
        if (empty($uid)) {
            $this->error(ErrorCodeService::USER_REQUIRED_ERROR);
        }
        if (empty($member_id)) {
            $this->error(ErrorCodeService::MEMBER_REQUIRED_ERROR);
        }
        $member_id = GlobalService::getGlobalKey('member_id');
        // 查询会员所处的会话id集合
        $idArr_A = FresnsDialogs::where('a_member_id', $member_id)->where('a_is_display', 1)->pluck('id')->toArray();
        $idArr_B = FresnsDialogs::where('b_member_id', $member_id)->where('b_is_display', 1)->pluck('id')->toArray();
        $idArr = array_merge($idArr_A, $idArr_B);
        // dd($idArr);
        $ids = implode(',', $idArr);
        // dd($ids);
        $page = $request->input('page', 1) ?? 1;
        $pageSize = $request->input('pageSize', 30) ?? 30;
        $FresnsDialogsService = new FresnsDialogsService();
        $request->offsetSet('currentPage', $page);
        $request->offsetSet('ids', $ids);
        $request->offsetSet('pageSize', $pageSize);
        $FresnsDialogsService->setResource(DialogsResource::class);
        $list = $FresnsDialogsService->searchData();
        $data = [
            'pagination' => $list['pagination'],
            'detail' => $list['list'],
        ];
        $this->success($data);
    }

    // 获取消息列表
    public function message_lists(Request $request)
    {
        $table = FresnsDialogsConfig::CFG_TABLE;
        $rule = [
            'dialogId' => [
                'required',
                'numeric',
                "exists:{$table},id",
            ],
        ];
        ValidateService::validateRule($request, $rule);
        $uid = $this->uid;
        $member_id = $this->mid;
        if (empty($uid)) {
            $this->error(ErrorCodeService::USER_REQUIRED_ERROR);
        }
        if (empty($member_id)) {
            $this->error(ErrorCodeService::MEMBER_REQUIRED_ERROR);
        }
        $mid = GlobalService::getGlobalKey('member_id');
        // dd($mid);
        $dialogId = $request->input('dialogId');
        // 查询会员所处的消息id集合
        $send_member_idArr = FresnsDialogMessages::where('dialog_id', $dialogId)->where('send_member_id',
            $mid)->where('send_deleted_at', null)->pluck('id')->toArray();
        $recv_member_idArr = FresnsDialogMessages::where('dialog_id', $dialogId)->where('recv_member_id',
            $mid)->where('recv_deleted_at', null)->pluck('id')->toArray();
        $idArr = array_merge($send_member_idArr, $recv_member_idArr);
        // dd($idArr);
        $ids = implode(',', $idArr);
        // 获取用户是成员A还是成员B
        $dialogsInfo = FresnsDialogs::where('id', $dialogId)->first();
        // dump($is_member_A);
        if ($dialogsInfo['a_member_id'] == $mid) {
            $member_id = $dialogsInfo['b_member_id'];
        } else {
            if ($dialogsInfo['b_member_id'] != $mid) {
                $this->error(ErrorCodeService::DIALOG_ERROR);
            }
            $member_id = $dialogsInfo['a_member_id'];
        }
        $memberInfo = DB::table(FresnsMembersConfig::CFG_TABLE)->where('id', $member_id)->first();
        $member = [];
        $member['deactivate'] = false;
        $member['mid'] = "";
        $member['mname'] = "";
        $member['nickname'] = "";
        $member['avatar'] = $memberInfo->avatar_file_url ?? "";
        // 为空用默认头像
        if (empty($member['avatar'])) {
            $defaultIcon = ApiConfigHelper::getConfigByItemKey(ContentConfig::DEFAULT_AVATAR);
            $member['avatar'] = $defaultIcon;
        }
        // 已注销头像 deactivate_avatar 键值"
        if ($memberInfo->deleted_at != null) {
            $deactivateAvatar = ApiConfigHelper::getConfigByItemKey(ContentConfig::DEACTIVATE_AVATAR);
            $member['avatar'] = $deactivateAvatar;
        }
        $member['avatar'] = ApiFileHelper::getImageSignUrl($member['avatar']);
        $member['decorate'] = "";
        $member['verifiedStatus'] = "";
        $member['verifiedIcon'] = "";
        if ($memberInfo) {
            if ($memberInfo->deleted_at == null) {
                $member['deactivate'] = true;
                $member['mid'] = $member_id;
                $member['mname'] = $memberInfo->name;
                $member['nickname'] = $memberInfo->nickname;
                // $member['decorate'] = $memberInfo->decorate_file_url;
                $member['decorate'] = ApiFileHelper::getImageSignUrlByFileIdUrl($memberInfo->decorate_file_id,
                    $memberInfo->decorate_file_url);
                $member['verifiedStatus'] = $memberInfo->verified_status;
                // $member['verifiedIcon'] = $memberInfo->verified_file_url;
                $member['verifiedIcon'] = ApiFileHelper::getImageSignUrlByFileIdUrl($memberInfo->verified_file_id,
                    $memberInfo->verified_file_url);
            }
        }

        $dialogId = $request->input('dialogId');
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 50);
        $FresnsDialogsService = new FresnsDialogMessagesService();
        $request->offsetSet('currentPage', $page);
        // $request->offsetSet('dialog_id', $dialogId);
        $request->offsetSet('ids', $ids);
        $request->offsetSet('pageSize', $pageSize);
        $FresnsDialogsService->setResource(DialogsMessageResource::class);
        $list = $FresnsDialogsService->searchData();
        $data = [
            'pagination' => $list['pagination'],
            'dialogId' => $dialogId,
            'member' => $member,
            'lists' => $list['list'],

        ];
        $this->success($data);
    }

    // 更新阅读状态[会话]
    public function message_read(Request $request)
    {
        $table = FresnsDialogsConfig::CFG_TABLE;
        $rule = [
            'dialogId' => [
                'required',
                'numeric',
                "exists:{$table},id",
            ],
        ];
        ValidateService::validateRule($request, $rule);
        $mid = GlobalService::getGlobalKey('member_id');
        $dialogId = $request->input('dialogId');
        // 会话是否为该成员所有
        $aCount = FresnsDialogs::where('a_member_id', $mid)->where('id', $dialogId)->count();
        $bCount = FresnsDialogs::where('b_member_id', $mid)->where('id', $dialogId)->count();
        if ($aCount == 0 && $bCount == 0) {
            $this->error(ErrorCodeService::DIALOG_ERROR);
        }
        // 收信者-阅读时间更新
        FresnsDialogMessages::where('dialog_id', $dialogId)->where('recv_member_id',
            $mid)->update(['recv_read_at' => date('Y-m-d H:i:s')]);
        // dialogs  status更新
        $is_member_A = FresnsDialogs::where('a_member_id', $mid)->where('id', $dialogId)->count();
        // dump($is_member_A);
        if ($is_member_A > 0) {
            FresnsDialogs::where('id', $dialogId)->update(['a_status' => 2]);
        } else {
            FresnsDialogs::where('id', $dialogId)->update(['b_status' => 2]);
        }
        $this->success();
    }

    // 发送消息
    public function message_send(Request $request)
    {
        $table = FresnsMembersConfig::CFG_TABLE;
        $mid = GlobalService::getGlobalKey('member_id');
        $rule = [
            'recvMid' => "required|exists:{$table},uuid|not_in:{$mid}",
            'message' => 'required_without:fid',
            'fid' => 'required_without:message',
        ];
        ValidateService::validateRule($request, $rule);
        // 验证提交参数
        $checkInfo = AmChecker::checkSendMessage($mid);
        // dd($checkInfo);
        if (is_array($checkInfo)) {
            return $this->errorCheckInfo($checkInfo);
        }
        // 需要先判断成员主角色是否有权发送私信（member_roles > permission > dialog=true）

        // // 如果对方已经注销（members > deleted_at），不可以发送。
        $recvMid = $request->input('recvMid');
        $message = $request->input('message', null);
        $fid = $request->input('fid', null);
        // if($message && $fid){
        //     $this->error(ErrorCodeService::FILE_OR_MESSAGE_ERROR);
        // }
        if ($fid) {
            $filesInfo = FresnsFiles::Where('uuid', $fid)->first();
            if (!$filesInfo) {
                $this->error(ErrorCodeService::FILES_ERROR);
            }
            $fileId = $filesInfo->id;
            $fileType = $filesInfo->type;
            $file_type = 'image';
            switch ($fileType) {
                case '2':
                    $file_type = 'video';
                    break;
                case '3':
                    $file_type = 'audio';
                    break;
                case '3':
                    $file_type = 'doc';
                    break;
                default:
                    $file_type = 'image';
                    break;
            }
        }
        if ($message) {
            $message = ApiCommonHelper::stopWords($message);
            if (!$message) {
                $this->error(ErrorCodeService::DIALOG_WORD_ERROR);
            }
        }
        $recvMemberInfo = FresnsMembers::where('uuid', $recvMid)->first();
        $recvMid = $recvMemberInfo['id'];
        // 查询会话id 没有则新建
        $input1 = [
            'a_member_id' => $mid,
            'b_member_id' => $recvMid,
        ];
        $dialogs = FresnsDialogs::where($input1)->first();
        if (!$dialogs) {
            $input2 = [
                'b_member_id' => $mid,
                'a_member_id' => $recvMid,
            ];
            $dialogs = FresnsDialogs::where($input2)->first();
            if (!$dialogs) {
                $input_dialogs = [
                    'a_member_id' => $mid,
                    'b_member_id' => $recvMid,
                ];
                $dialogsId = (new FresnsDialogs())->store($input_dialogs);
                // $sessionLogId = GlobalService::getGlobalSessionKey('session_log_id');
                // if($sessionLogId){
                //     FresnsSessionLogs::where('id',$sessionLogId)->update(['object_result' => AmConfig::OBJECT_SUCCESS,'object_order_id' => $dialogsId]);
                // }
            } else {
                $dialogsId = $dialogs['id'];
            }
        } else {
            $dialogsId = $dialogs['id'];
        }
        // dd($dialogsId);
        // 消息表入库
        $fileId = $fileId ?? null;
        $input_message = [
            'dialog_id' => $dialogsId,
            'send_member_id' => $mid,
            'message_text' => $message,
            'file_id' => $fileId,
            'recv_member_id' => $recvMid,
        ];
        $messageId = (new FresnsDialogMessages())->store($input_message);
        $sessionLogId = GlobalService::getGlobalSessionKey('session_log_id');
        if ($sessionLogId) {
            FresnsSessionLogs::where('id', $sessionLogId)->update([
                'object_result' => AmConfig::OBJECT_SUCCESS,
                'object_order_id' => $messageId
            ]);
        }
        // 更新dialogs表
        $count = FresnsDialogs::where('id', $dialogsId)->where('a_member_id', $mid)->count();
        if ($count > 0) {
            // 为消息 更新latest_message_brief 为文件不更新
            if ($fid) {
                $update_input = [
                    'latest_message_id' => $messageId,
                    'latest_message_time' => date('Y-m-d H:i:s'),
                    'latest_message_brief' => "[{$file_type}]",
                    'b_status' => 1,
                    'b_is_display' => 1,
                    'a_is_display' => 1,
                ];
            } else {
                $update_input = [
                    'latest_message_id' => $messageId,
                    'latest_message_time' => date('Y-m-d H:i:s'),
                    'latest_message_brief' => $message,
                    'b_status' => 1,
                    'b_is_display' => 1,
                    'a_is_display' => 1,
                ];
            }
        } else {
            if ($fid) {
                $update_input = [
                    'latest_message_id' => $messageId,
                    'latest_message_time' => date('Y-m-d H:i:s'),
                    'latest_message_brief' => "[{$file_type}]",
                    'a_status' => 1,
                    'a_is_display' => 1,
                    'b_is_display' => 1,
                ];
            } else {
                $update_input = [
                    'latest_message_id' => $messageId,
                    'latest_message_time' => date('Y-m-d H:i:s'),
                    'latest_message_brief' => $message,
                    'a_status' => 1,
                    'a_is_display' => 1,
                    'b_is_display' => 1,
                ];
            }
        }
        FresnsDialogs::where('id', $dialogsId)->update($update_input);
        $this->success();
    }

    // 删除消息(会话)
    public function dialog_delete(Request $request)
    {
        $table = FresnsDialogsConfig::CFG_TABLE;
        $messageTable = FresnsDialogMessagesConfig::CFG_TABLE;
        $rule = [
            'dialogId' => [
                "exists:{$table},id",
                "required_without:messageId"
            ],
            'messageId' => [
                'array',
                "required_without:dialogId"
            ]
        ];
        ValidateService::validateRule($request, $rule);
        $mid = GlobalService::getGlobalKey('member_id');
        $dialogId = $request->input('dialogId', "");
        $messageIdArr = $request->input('messageId', "");
        if ($dialogId) {
            if ($messageIdArr) {
                $this->error(ErrorCodeService::DIALOG_OR_MESSAGE_ERROR);
            }
            // 会话是否为该成员所有
            $aCount = FresnsDialogs::where('a_member_id', $mid)->where('id', $dialogId)->count();
            $bCount = FresnsDialogs::where('b_member_id', $mid)->where('id', $dialogId)->count();
            if ($aCount == 0 && $bCount == 0) {
                $this->error(ErrorCodeService::DIALOG_ERROR);
            }
            // 会话列表隐藏（）
            $count = FresnsDialogs::where('id', $dialogId)->where('a_member_id', $mid)->count();
            if ($count > 0) {
                FresnsDialogs::where('id', $dialogId)->update(['a_is_display' => 0]);
            } else {
                FresnsDialogs::where('id', $dialogId)->update(['b_is_display' => 0]);
            }
            // $Message_count = FresnsDialogMessages::where('dialog_id',$dialogId)->where('send_member_id',$mid)->count();
            // 消息列表相关也删除
            // if($Message_count > 0){
            FresnsDialogMessages::where('dialog_id', $dialogId)->where('send_member_id',
                $mid)->update(['send_deleted_at' => date('Y-m-d H:i:s')]);
            // }else{
            FresnsDialogMessages::where('dialog_id', $dialogId)->where('recv_member_id',
                $mid)->update(['recv_deleted_at' => date('Y-m-d H:i:s')]);
            // }
            $this->success();
        }
        if ($messageIdArr) {
            foreach ($messageIdArr as $messageId) {
                // 判断用户是发送者还是收信者
                $count = FresnsDialogMessages::where('id', $messageId)->where('send_member_id', $mid)->count();
                $recvCount = FresnsDialogMessages::where('id', $messageId)->where('recv_member_id', $mid)->count();
                if ($count == 0 && $recvCount == 0) {
                    $this->error(ErrorCodeService::DELETED_NOTIFY_ERROR);
                }
                if ($count > 0) {
                    $dialogMessageCount = FresnsDialogMessages::where('id', $messageId)->where('send_deleted_at', '!=',
                        null)->count();
                    if ($dialogMessageCount > 0) {
                        $this->error(ErrorCodeService::DIALOGS_MESSAGE_ERROR);
                    }
                    FresnsDialogMessages::where('id', $messageId)->update(['send_deleted_at' => date('Y-m-d H:i:s')]);
                } else {
                    $dialogMessageCount = FresnsDialogMessages::where('id', $messageId)->where('recv_deleted_at', '!=',
                        null)->count();
                    if ($dialogMessageCount > 0) {
                        $this->error(ErrorCodeService::DIALOGS_MESSAGE_ERROR);
                    }
                    FresnsDialogMessages::where('id', $messageId)->update(['recv_deleted_at' => date('Y-m-d H:i:s')]);
                }
            }
            $this->success();
        }
    }

    // 数据是否为用户所有
    public static function isExsitMember($idArr, $table, $field, $field_value)
    {
        if (!is_array($idArr)) {
            return false;
        }

        if (count($idArr) == 0) {
            return false;
        }
        // $conn = DBHelper::getConnectionName($table);
        foreach ($idArr as $id) {
            $queryCount = DB::table($table)->where('id', $id)->where($field, $field_value)->count();
            if ($queryCount == 0) {
                return false;
            }
        }
        return true;
    }
}
