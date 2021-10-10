<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Message;

use App\Base\Resources\BaseAdminResource;
use App\Http\FresnsApi\Content\FsConfig as ContentConfig;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsApi\Helpers\ApiFileHelper;
use App\Http\FresnsDb\FresnsComments\FresnsComments;
use App\Http\FresnsDb\FresnsMembers\FresnsMembers;
use App\Http\FresnsDb\FresnsMembers\FresnsMembersConfig;
use App\Http\FresnsDb\FresnsNotifies\FresnsNotifiesConfig;
use App\Http\FresnsDb\FresnsPosts\FresnsPosts;
use Illuminate\Support\Facades\DB;

/**
 * List resource config handle.
 */
class FresnsNotifiesResource extends BaseAdminResource
{
    public function toArray($request)
    {
        // Form Field
        $formMap = FresnsNotifiesConfig::FORM_FIELDS_MAP;
        $formMapFieldsArr = [];
        foreach ($formMap as $k => $dbField) {
            $formMapFieldsArr[$dbField] = $this->$dbField;
        }

        // Notify Data
        $messageId = $this->id;
        $sourceType = $this->source_type;
        $sourceClass = $this->source_class;
        $sourceId = $this->source_id;
        if ($sourceClass == 1) {
            $data = FresnsPosts::find($sourceId);
        } else {
            $data = FresnsComments::find($sourceId);
        }
        $member = DB::table(FresnsMembersConfig::CFG_TABLE)->where('id', $this->source_member_id)->first();
        $sourceMember = [];
        $avatar = $member->avatar_file_url ?? '';
        if ($member) {
            // Default avatar when members have no avatar
            if (empty($avatar)) {
                $defaultIcon = ApiConfigHelper::getConfigByItemKey(ContentConfig::DEFAULT_AVATAR);
                $avatar = $defaultIcon;
            }
            // The avatar displayed when a member has been deleted
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
            $member = FresnsMembers::find($this->source_member_id);
            $sourceMember = 
                [
                    'mid' => $member['uuid'] ?? '',
                    'mname' => $member->name ?? '',
                    'nickname' => $member->nickname ?? '',
                    'avatar' => $avatar,
                    'decorate' => ApiFileHelper::getImageSignUrlByFileIdUrl($member->decorate_file_id, $member->decorate_file_url),
                    'verifiedStatus' => $member->verified_status ?? 1,
                    'verifiedIcon' => ApiFileHelper::getImageSignUrlByFileIdUrl($member->verified_file_id, $member->verified_file_url),
            ];
        }
        $sourceBrief = $this->source_brief;
        $status = $this->status;

        // Default Field
        $default = [
            'nitifyId' => $messageId,
            'sourceType' => $sourceType,
            'sourceClass' => $sourceClass,
            'sourceUuId' => $data['uuid'] ?? '',
            'sourceMember' => $sourceMember,
            'sourceBrief' => $sourceBrief,
            'status' => $status,
        ];

        // Merger
        $arr = $default;

        return $arr;
    }
}
