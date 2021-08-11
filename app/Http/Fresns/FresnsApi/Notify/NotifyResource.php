<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Notify;

use App\Base\Resources\BaseAdminResource;
use App\Http\Fresns\FresnsNotifies\FresnsNotifiesConfig;
use App\Http\Fresns\FresnsMembers\FresnsMembers;
use App\Http\Fresns\FresnsApi\Helpers\ApiFileHelper;
use App\Http\Fresns\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\Fresns\FresnsApi\Content\AmConfig as ContentConfig;
use Illuminate\Support\Facades\DB;
use App\Http\Fresns\FresnsMembers\FresnsMembersConfig;

class NotifyResource extends BaseAdminResource
{

    public function toArray($request)
    {
        // form 字段
        $formMap = FresnsNotifiesConfig::FORM_FIELDS_MAP;
        $formMapFieldsArr = [];
        foreach ($formMap as $k => $dbField) {
            $formMapFieldsArr[$dbField] = $this->$dbField;
        }
        $messageId = $this->id;
        $type = $this->source_type;
        $class = $this->source_class;
        $sourceId = $this->source_id;
        // $member = FresnsMembers::find($this->source_mid);
        $member = DB::table(FresnsMembersConfig::CFG_TABLE)->where('id', $this->source_mid)->first();
        $sourceMember = [];
        $avatar = $member->avatar_file_url ?? "";
        if ($member) {
            // 为空用默认头像
            if (empty($avatar)) {
                $defaultIcon = ApiConfigHelper::getConfigByItemKey(ContentConfig::DEFAULT_AVATAR);
                $avatar = $defaultIcon;
            }
            // 已注销头像 deactivate_avatar 键值"
            if ($member) {
                if ($member->deleted_at != null) {
                    $deactivateAvatar = ApiConfigHelper::getConfigByItemKey(ContentConfig::DEACTIVATE_AVATAR);
                    $avatar = $deactivateAvatar;
                }
            } else {
                $deactivateAvatar = ApiConfigHelper::getConfigByItemKey(ContentConfig::DEACTIVATE_AVATAR);
                $avatar = $deactivateAvatar;
            }
            $avatar = ApiFileHelper::getImageSignUrl($avatar);
            $sourceMember = [
                [
                    'mid' => $this->source_mid,
                    'mname' => $member->name ?? "",
                    'nickname' => $member->nickname ?? "",
                    'avatar' => $avatar,
                    // 'avatar' =>  ApiFileHelper::getImageSignUrlByFileIdUrl($member['avatar_file_id'],$member['avatar_file_url']),
                    // 'decorate' =>  $member['decorate_file_url'] ?? "",
                    'decorate' => ApiFileHelper::getImageSignUrlByFileIdUrl($member->decorate_file_id,
                        $member->decorate_file_url),
                    'verifiedStatus' => $member->verified_status ?? "",
                    // 'verifiedIcon' => $member['verified_file_url'] ?? ""
                    'verifiedIcon' => ApiFileHelper::getImageSignUrlByFileIdUrl($member->verified_file_id,
                        $member->verified_file_url),
                ]
            ];
        }

        $sourceBrief = $this->source_brief;
        $status = $this->status;
        // 默认字段
        $default = [
            'messageId' => $messageId,
            'type' => $type,
            'class' => $class,
            'sourceId' => $sourceId,
            'sourceMember' => $sourceMember,
            'sourceBrief' => $sourceBrief,
            'status' => $status,
        ];

        // 合并
        $arr = $default;

        return $arr;
    }
}

