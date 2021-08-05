<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsMemberFollows;

use App\Base\Services\BaseAdminService;

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

    //向member_follow表添加数据
    public static function addMemberFollow($mid, $markTarget, $markId)
    {
        $input = [
            'member_id' => $mid,
            'follow_type' => $markTarget,
            'follow_id' => $markId,
        ];
        FresnsMemberFollows::insert($input);
    }

    public static function delMemberFollow($mid, $markTarget, $markId)
    {
        FresnsMemberFollows::where('member_id', $mid)->where('follow_type', $markTarget)->where('follow_id',
            $markId)->delete();
    }

}