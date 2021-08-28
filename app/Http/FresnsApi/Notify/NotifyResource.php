<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Notify;

use App\Base\Resources\BaseAdminResource;
use App\Http\FresnsApi\Content\AmConfig as ContentConfig;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsApi\Helpers\ApiFileHelper;
use App\Http\FresnsDb\FresnsComments\FresnsComments;
use App\Http\FresnsDb\FresnsMembers\FresnsMembers;
use App\Http\FresnsDb\FresnsMembers\FresnsMembersConfig;
use App\Http\FresnsDb\FresnsNotifies\FresnsNotifiesConfig;
use App\Http\FresnsDb\FresnsPosts\FresnsPosts;
use Illuminate\Support\Facades\DB;

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
        if ($class == 1) {
            $data = FresnsPosts::find($sourceId);
        } else {
            $data = FresnsComments::find($sourceId);
        }
        // $member = FresnsMembers::find($this->source_mid);
        $member = DB::table(FresnsMembersConfig::CFG_TABLE)->where('id', $this->source_mid)->first();
        $sourceMember = [];
        $avatar = $member->avatar_file_url ?? '';
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
            $member = FresnsMembers::find($this->source_mid);
            $sourceMember = [
                [
                    'mid' => $member['uuid'] ?? '',
                    'mname' => $member->name ?? '',
                    'nickname' => $member->nickname ?? '',
                    'avatar' => $avatar,
                    // 'avatar' =>  ApiFileHelper::getImageSignUrlByFileIdUrl($member['avatar_file_id'],$member['avatar_file_url']),
                    // 'decorate' =>  $member['decorate_file_url'] ?? "",
                    'decorate' => ApiFileHelper::getImageSignUrlByFileIdUrl($member->decorate_file_id,
                        $member->decorate_file_url),
                    'verifiedStatus' => $member->verified_status ?? '',
                    // 'verifiedIcon' => $member['verified_file_url'] ?? ""
                    'verifiedIcon' => ApiFileHelper::getImageSignUrlByFileIdUrl($member->verified_file_id,
                        $member->verified_file_url),
                ],
            ];
        }

        $sourceBrief = $this->source_brief;
        $status = $this->status;
        // 默认字段
        $default = [
            'nitifyId' => $messageId,
            'type' => $type,
            'class' => $class,
            'sourceUuId' => $data['uuid'] ?? '',
            'sourceMember' => $sourceMember,
            'sourceBrief' => $sourceBrief,
            'status' => $status,
        ];

        // 合并
        $arr = $default;

        return $arr;
    }
}
