<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsMemberLikes;

use App\Base\Services\BaseAdminService;
use App\Http\FresnsDb\FresnsMemberStats\FresnsMemberStatsConfig;
use Illuminate\Support\Facades\DB;

class AmService extends BaseAdminService
{
    public function __construct()
    {
        $this->model = new AmModel();
        $this->resource = AmResource::class;
        $this->resourceDetail = AmResourceDetail::class;
    }

    public function common()
    {
        $common = parent::common();
        return $common;
    }

    // Add data to the member_likes table
    public static function addMemberLike($member_id, $mark_target, $mark_id, $member_count = null, $me_count = null)
    {
        $input = [
            'member_id' => $member_id,
            'like_type' => $mark_target,
            'like_id' => $mark_id,
        ];

        FresnsMemberLikes::insert($input);
        if ($member_count) {
            DB::table(FresnsMemberStatsConfig::CFG_TABLE)->where('member_id', $member_id)->increment($member_count);
        }
        if ($me_count) {
            DB::table(FresnsMemberStatsConfig::CFG_TABLE)->where('member_id', $mark_id)->increment($me_count);
        }
    }

    // Delete Like Data
    public static function deleMemberLike($member_id, $mark_target, $mark_id)
    {
        DB::table(FresnsMemberLikesConfig::CFG_TABLE)->where('like_type', $mark_target)->where('member_id', $member_id)->where('like_id', $mark_id)->update(['deleted_at' => date('Y-m-d H:i:s', time())]);
    }
}
