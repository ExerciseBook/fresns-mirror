<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsMemberShields;

use App\Base\Services\BaseAdminService;
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
        $common['selectOption'] = AmConfig::TEST_SELECT_OPTION;

        return $common;
    }

    public static function addMemberShields($mid, $markTarget, $markId)
    {
        $input = [
            'member_id' => $mid,
            'shield_type' => $markTarget,
            'shield_id' => $markId,
        ];
        FresnsMemberShields::insert($input);
    }

    public static function delMemberShields($mid, $markTarget, $markId)
    {
        DB::table(FresnsMemberShieldsConfig::CFG_TABLE)->where('shield_type', $markTarget)->where('member_id',
            $mid)->where('shield_id', $markId)->update(['deleted_at' => date('Y-m-d H:i:s', time())]);
    }
}
