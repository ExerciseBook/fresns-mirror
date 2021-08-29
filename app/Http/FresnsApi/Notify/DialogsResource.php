<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Notify;

use App\Base\Resources\BaseAdminResource;
use App\Http\Center\Common\GlobalService;
use App\Http\FresnsApi\Content\AmConfig as ContentConfig;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsApi\Helpers\ApiFileHelper;
use App\Http\FresnsDb\FresnsDialogMessages\FresnsDialogMessages;
use App\Http\FresnsDb\FresnsDialogs\FresnsDialogs;
use App\Http\FresnsDb\FresnsDialogs\FresnsDialogsConfig;
use App\Http\FresnsDb\FresnsMembers\FresnsMembersConfig;
use Illuminate\Support\Facades\DB;

class DialogsResource extends BaseAdminResource
{
    public function toArray($request)
    {
        // dd(1);
        // form 字段
        $formMap = FresnsDialogsConfig::FORM_FIELDS_MAP;
        $formMapFieldsArr = [];
        foreach ($formMap as $k => $dbField) {
            $formMapFieldsArr[$dbField] = $this->$dbField;
        }
        $dialogId = $this->id;
        // $mid = request()->header("mid");
        $mid = GlobalService::getGlobalKey('member_id');

        // 获取用户是成员A还是成员B
        $is_member_A = FresnsDialogs::where('a_member_id', $mid)->where('id', $this->id)->count();
        // dump($is_member_A);
        if ($is_member_A > 0) {
            $member_id = $this->b_member_id;
            $status = $this->a_status;
        } else {
            $member_id = $this->a_member_id;
            $status = $this->b_status;
        }
        $memberInfo = DB::table(FresnsMembersConfig::CFG_TABLE)->where('id', $member_id)->first();
        // dd($memberInfo);
        $member = [];
        $member['deactivate'] = false;
        $member['mid'] = '';
        $member['mname'] = '';
        $member['nickname'] = '';
        $member['avatar'] = $memberInfo->avatar_file_url ?? '';
        $member['decorate'] = '';
        $member['verifiedStatus'] = '';
        $member['verifiedIcon'] = '';
        // 为空用默认头像
        if (empty($member['avatar'])) {
            $defaultIcon = ApiConfigHelper::getConfigByItemKey(AmConfig::DEFAULT_AVATAR);
            $member['avatar'] = $defaultIcon;
        }
        // 已注销头像 deactivate_avatar 键值"
        if ($memberInfo) {
            if ($memberInfo->deleted_at != null) {
                $deactivateAvatar = ApiConfigHelper::getConfigByItemKey(AmConfig::DEACTIVATE_AVATAR);
                $member['avatar'] = $deactivateAvatar;
            }
            if ($memberInfo->deleted_at == null) {
                $member['deactivate'] = true;
                $member['mid'] = $memberInfo->uuid;
                $member['mname'] = $memberInfo->name;
                $member['nickname'] = $memberInfo->nickname;
                $member['avatar'] = ApiFileHelper::getImageSignUrl($member['avatar']);
                // $member['decorate'] = $memberInfo->decorate_file_url;
                $member['decorate'] = ApiFileHelper::getImageSignUrlByFileIdUrl($memberInfo->decorate_file_id,
                    $memberInfo->decorate_file_url);
                $member['verifiedStatus'] = $memberInfo->verified_status;
                // $member['verifiedIcon'] = $memberInfo->verified_file_url;
                $member['verifiedIcon'] = ApiFileHelper::getImageSignUrlByFileIdUrl($memberInfo->verified_file_id,
                    $memberInfo->verified_file_url);
            }
        }

        $messageId = $this->latest_message_id;
        $messageTime = $this->latest_message_time;
        $messageBrief = $this->latest_message_brief;

        // 未读数量
        $messageUnread = 0;
        if ($status == 1) {
            $messageUnread = FresnsDialogMessages::where('recv_member_id', $mid)->where('recv_read_at', null)->count();
        }
        // 默认字段
        $default = [
            'dialogId' => $dialogId,
            'member' => $member,
            'messageId' => $messageId,
            'messageTime' => $messageTime,
            'messageBrief' => $messageBrief,
            'messageUnread' => $messageUnread,
            'status' => $status,
        ];

        // 合并
        $arr = $default;

        return $arr;
    }
}
