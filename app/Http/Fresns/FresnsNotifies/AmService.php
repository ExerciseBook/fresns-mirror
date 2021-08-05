<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsNotifies;

use App\Base\Services\BaseAdminService;
use App\Http\Fresns\FresnsMemberFollows\FresnsMemberFollowsConfig;
use App\Http\Fresns\FresnsMemberLikes\FresnsMemberLikesConfig;
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

    /**
     * 插入信息
     *
     * @param [type] $mark_id 对象id
     * @param [type] $member_id 当前用户
     * @param [type] $source_type 1.系统 / 2.关注 / 3.点赞 / 4.评论 / 5.提及（艾特） / 6.推荐
     * @param [type] $markTarget 1.成员 / 2.小组 / 3.话题 / 4.帖子 / 5.评论
     * @param [type] $source_brief 摘要内容
     * @param [type] $source_class 触发内容种类    1.帖子 / 2.评论
     * @param [type] $source_id 帖子或评论 ID 关联字段 posts > id，关联字段 comments > id，这条通知来源由哪个帖子或评论
     * @return void
     */
    public static function markNotifies(
        $mark_id,
        $member_id,
        $source_type,
        $markTarget = null,
        $source_brief = null,
        $source_class = null,
        $source_id = null
    ) {
        //对同一个对象（点赞某人或关注某人），一天内只生产一次通知，避免频繁建立和取消的操作。source_type：2-关注 3-点赞
        $count = FresnsNotifies::where('member_id', $mark_id)->where('source_type', $source_type)->where('source_class',
            $source_class)->where('source_id', $source_id)->whereDate('created_at', date('Y-m-d', time()))->count();
        if ($count >= 1) {
            return false;
        }
        // if($source_type == 2 || $source_type == 3){

        //     switch ($source_type) {
        //         case 2:
        //             $count = DB::table(FresnsMemberFollowsConfig::CFG_TABLE)->where('member_id',$member_id)->where('follow_id',$mark_id)->where('follow_type',$markTarget)->whereDate('created_at',date('Y-m-d',time()))->where('deleted_at',NULL)->count();

        //             if($count > 1){
        //                 return false;
        //             }

        //             break;
        //         default:
        //             $count = DB::table(FresnsMemberLikesConfig::CFG_TABLE)->where('member_id',$member_id)->where('like_id',$mark_id)->where('like_type',$markTarget)->whereDate('created_at',date('Y-m-d',time()))->where('deleted_at',NULL)->count();

        //             if($count > 1){
        //                 return false;
        //             }
        //             break;
        //     }

        // }
        // dd(111);
        $input = [
            'member_id' => $mark_id,
            'source_type' => $source_type,
            'source_brief' => $source_brief,
            'source_id' => $source_id,
            'source_class' => $source_class,
            'source_mid' => $member_id
        ];
        FresnsNotifies::insert($input);
    }

}