<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsPosts;

use App\Base\Models\BaseAdminModel;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsDb\FresnsGroups\FresnsGroups;
use App\Http\FresnsDb\FresnsHashtagLinkeds\FresnsHashtagLinkeds;
use App\Http\FresnsDb\FresnsHashtagLinkeds\FresnsHashtagLinkedsConfig;
use App\Http\FresnsDb\FresnsMemberFollows\FresnsMemberFollows;
use App\Http\FresnsDb\FresnsMemberFollows\FresnsMemberFollowsConfig;
use App\Http\FresnsDb\FresnsMembers\FresnsMembers;
use App\Http\FresnsDb\FresnsMemberShields\FresnsMemberShieldsConfig;
use App\Http\FresnsDb\FresnsPostAppends\FresnsPostAppendsConfig;
use App\Http\Center\AmGlobal\GlobalService;
use Illuminate\Support\Facades\DB;

class AmModel extends BaseAdminModel
{
    protected $table = AmConfig::CFG_TABLE;

    // 前台表单字段映射
    public function formFieldsMap()
    {
        return AmConfig::FORM_FIELDS_MAP;
    }

    // 新增搜索条件
    public function getAddedSearchableFields()
    {
        return AmConfig::ADDED_SEARCHABLE_FIELDS;
    }

    // hook-添加之后
    public function hookStoreAfter($id)
    {
    }

    public function getRawSqlQuery()
    {
        $memberShieldsTable = FresnsMemberShieldsConfig::CFG_TABLE;
        $memberFollowTable = FresnsMemberFollowsConfig::CFG_TABLE;
        $postTable = FresnsPostsConfig::CFG_TABLE;
        $append = FresnsPostAppendsConfig::CFG_TABLE;
        /**
         * 如果是非公开小组的帖子，不是小组成员，不输出。
         *过滤屏蔽对象的帖子（成员、小组、话题、帖子），屏蔽对象的帖子不输出。
         *searchKey 查询的是帖子标题（posts > title）和全量正文（post_appends > content）
         *searchType 留空代表输出所有内容。内容为插件 unikey 值，用于搜索包含指定插件扩展内容的帖子。
         *默认排序类型「time」，默认排序方式「降序」.
         */
        // 屏蔽的目标字段
        $request = request();
        $mid = GlobalService::getGlobalKey('member_id');

        // 如果是非公开小组的帖子，不是小组成员，不输出。
        $FresnsGroups = FresnsGroups::where('type_mode', 2)->where('type_find', 2)->pluck('id')->toArray();

        // $groupMember = FresnsMemberFollows::where('member_id',$mid)->where('follow_type',2)->pluck('follow_id')->toArray();
        $groupMember = DB::table($memberFollowTable)->where('member_id', $mid)->where('deleted_at',
            null)->where('follow_type', 2)->pluck('follow_id')->toArray();
        // dump($FresnsGroups);
        $noGroupArr = array_diff($FresnsGroups, $groupMember);
        // 过滤屏蔽对象的帖子（成员、小组、话题、帖子），屏蔽对象的帖子不输出。
        $memberShields = DB::table($memberShieldsTable)->where('member_id', $mid)->where('deleted_at',
            null)->where('shield_type', 1)->pluck('shield_id')->toArray();
        $GroupShields = DB::table($memberShieldsTable)->where('member_id', $mid)->where('deleted_at',
            null)->where('shield_type', 2)->pluck('shield_id')->toArray();
        $shieldshashtags = DB::table($memberShieldsTable)->where('member_id', $mid)->where('deleted_at',
            null)->where('shield_type', 3)->pluck('shield_id')->toArray();
        // $noPostHashtags = FresnsHashtagLinkeds::where('linked_type',1)->whereIn('hashtag_id',$shieldshashtags)->pluck('linked_id')->toArray();
        $noPostHashtags = DB::table(FresnsHashtagLinkedsConfig::CFG_TABLE)->where('linked_type', 1)->where('deleted_at',
            null)->whereIn('hashtag_id', $shieldshashtags)->pluck('linked_id')->toArray();
        $commentShields = DB::table($memberShieldsTable)->where('member_id', $mid)->where('deleted_at',
            null)->where('shield_type', 4)->pluck('shield_id')->toArray();
        $query = DB::table("$postTable as post")->select('post.*')
            ->join("$append as append", 'post.id', '=', 'append.post_id')
            // ->whereNotIn('post.group_id',$noGroupArr)
            // ->whereNotIn('post.group_id',$GroupShields)
            ->whereNotIn('post.member_id', $memberShields)
            ->whereNotIn('post.id', $noPostHashtags)
            ->whereNotIn('post.id', $commentShields)
            ->where('post.deleted_at', null);
        // ->where('post.status',3);
        // dd($noGroupArr);
        // dump($memberShields);
        // dump($noPostHashtags);
        // dd($commentShields);
        if (! empty($noGroupArr)) {
            // dump($noGroupArr);
            // $query->whereNotIn('post.group_id',$noGroupArr);
            $postgroupIdArr = FresnsPosts::whereNotIn('group_id', $noGroupArr)->pluck('id')->toArray();
            $noPostgroupIdArr = FresnsPosts::where('group_id', null)->pluck('id')->toArray();
            // dd($postIdArr);
            $postIdArr = array_merge($postgroupIdArr, $noPostgroupIdArr);
            // dump($postIdArr);
            $query->whereIn('post.id', $postIdArr);
        }
        // dd($GroupShields);
        if (! empty($GroupShields)) {
            // dump($GroupShields);
            // $query->whereNotIn('post.group_id',$GroupShields);
            $postIdArr = FresnsPosts::whereNotIn('group_id', $GroupShields)->pluck('id')->toArray();
            // dd($postIdArr);
            $query->whereIn('post.id', $postIdArr);
        }
        // dd($noGroupArr);
        // dump($GroupShields);
        // 2、成员 members > expired_at 是否在有效期内（为空代表永久有效）。
        // 2.1、过期后内容不可见，不输出帖子列表。
        // 2.2、过期后，到期前的内容可见，输出到期日期前的帖子列表。
        // 2.3、在有效期内，继续往下判断。
        $site_mode = ApiConfigHelper::getConfigByItemKey('site_mode');
        if ($site_mode == 'private') {
            $memberInfo = FresnsMembers::find($mid);
            if (! empty($memberInfo['expired_at']) && (strtotime($memberInfo['expired_at'])) < time()) {
                $site_private_end = ApiConfigHelper::getConfigByItemKey('site_private_end');
                if ($site_private_end == 1) {
                    $query->where('post.member_id', '=', 0);
                }
                if ($site_private_end == 2) {
                    $query->where('post.created_at', '<=', $memberInfo['expired_at']);
                }
            }
        }

        // 公共参数
        // 搜索：关键词
        $searchKey = $request->input('searchKey');
        if ($searchKey) {
            $query->where('append.content', 'like', "%{$searchKey}%");
            $query->Orwhere('post.title', 'like', "%{$searchKey}%");
        }
        // 搜索类型（搜索类型扩展配置的参数）
        $searchType = $request->input('searchType');
        if ($searchType) {
            if ($searchType != 'all') {
                $query->where('post.type', 'like', "%{$searchType}%");
            }
        }
        // 指定范围：成员
        $searchMid = $request->input('searchMid');
        if ($searchMid) {
            // 后台是否允许查看别人的帖子
            $allowPost = ApiConfigHelper::getConfigByItemKey(AmConfig::IT_PUBLISH_POSTS) ?? true;
            // dd($allowPost);
            if (! $allowPost) {
                $query->where('post.member_id', '=', 0);
            } else {
                $memberInfo = FresnsMembers::where('uuid', $searchMid)->first();
                if ($memberInfo) {
                    $query->where('post.member_id', '=', $memberInfo['id']);
                } else {
                    $query->where('post.member_id', '=', 0);
                }
            }
        }
        // 指定范围：小组
        $searchGid = $request->input('searchGid');
        if ($searchGid) {
            $query->where('post.group_id', '=', $searchGid);
        }
        // 指定范围：话题
        $searchHuri = $request->input('searchHuri');
        if ($searchHuri) {
            $topicLinkArr = Db::table('hashtag_linkeds')->where('hashtag_id', $searchHuri)->where('linked_type',
                1)->pluck('linked_id')->toArray();

            $query->whereIn('post.id', $topicLinkArr);
        }
        // 置顶
        $searchEssence = $request->input('searchEssence');
        if ($searchEssence) {
            // $searchEssenceType = $searchEssence == 'false' ? [1] : [2,3];
            // dd($searchEssenceType);
            $query->where('post.sticky_status', $searchEssence);
        }
        // 精华
        $searchSticky = $request->input('searchSticky');
        if ($searchSticky) {
            // $searchStickyType = $searchSticky == 'false' ? [1] : [2,3];
            $query->where('post.essence_status', $searchSticky);
        }
        // viewCountGt
        $viewCountGt = $request->input('viewCountGt');
        if ($viewCountGt) {
            $query->where('post.view_count', '>=', $viewCountGt);
        }
        // viewCountLt
        $viewCountLt = $request->input('viewCountLt');
        if ($viewCountLt) {
            $query->where('post.view_count', '<=', $viewCountLt);
        }
        // likeCountGt
        $likeCountGt = $request->input('likeCountGt');
        if ($likeCountGt) {
            $query->where('post.like_count', '>=', $likeCountGt);
        }
        // likeCountLt
        $likeCountLt = $request->input('likeCountLt');
        // dd($likeCountLt);
        if ($likeCountLt) {
            // dd($likeCountLt);
            $query->where('post.like_count', '<=', $likeCountLt);
        }
        // followCountGt
        $followCountGt = $request->input('followCountGt');
        if ($followCountGt) {
            $query->where('post.follow_count', '>=', $followCountGt);
        }
        // followCountLt
        $followCountLt = $request->input('followCountLt');
        if ($followCountLt) {
            $query->where('post.follow_count', '<=', $followCountLt);
        }
        // shieldCountGt
        $shieldCountGt = $request->input('shieldCountGt');
        if ($shieldCountGt) {
            $query->where('post.shield_count', '>=', $shieldCountGt);
        }
        // shield_count
        $shieldCountLt = $request->input('shieldCountLt');
        if ($shieldCountLt) {
            $query->where('post.shield_count', '<=', $shieldCountLt);
        }
        // commentCountGt
        $commentCountGt = $request->input('commentCountGt');
        if ($commentCountGt) {
            $query->where('post.comment_count', '>=', $commentCountGt);
        }
        // commentCountLt
        $commentCountLt = $request->input('commentCountLt');
        if ($commentCountLt) {
            $query->where('post.comment_count', '<=', $commentCountLt);
        }
        // publishTimeGt
        $publishTimeGt = $request->input('publishTimeGt');
        if ($publishTimeGt) {
            $query->where('post.created_at', '>=', $publishTimeGt);
        }
        // publishTimeLt
        $publishTimeLt = $request->input('publishTimeLt');
        if ($publishTimeLt) {
            $query->where('post.created_at', '<=', $publishTimeLt);
        }
        // createdTimeGt
        $createdTimeGt = $request->input('createdTimeGt');
        if ($createdTimeGt) {
            $query->where('post.created_at', '>=', $createdTimeGt);
        }
        // createdTimeLt
        $createdTimeLt = $request->input('createdTimeLt');
        if ($createdTimeLt) {
            $query->where('post.created_at', '<=', $createdTimeLt);
        }
        // 排序处理
        $sortType = request()->input('sortType', '');
        $sortDirection = request()->input('sortDirection', 2);
        $sortWayType = $sortDirection == 2 ? 'DESC' : 'ASC';
        switch ($sortType) {
            case 'view':
                $query->orderBy('post.view_count', $sortWayType);
                break;
            case 'like':
                $query->orderBy('post.like_count', $sortWayType);
                break;
            case 'follow':
                $query->orderBy('post.follow_count', $sortWayType);
                break;
            case 'shield':
                $query->orderBy('post.shield_count', $sortWayType);
                break;
            case 'comment ':
                $query->orderBy('post.comment_count', $sortWayType);
                break;
            case 'time':
                $query->orderBy('post.created_at', $sortWayType);
                break;
            default:
                $query->orderBy('post.created_at', $sortWayType);
                break;
        }

        return $query;
    }

    // 搜索排序字段
    public function initOrderByFields()
    {
        $sortType = request()->input('sortType', '');
        $sortWay = request()->input('sortWay', 2);
        $sortWayType = $sortWay == 2 ? 'DESC' : 'ASC';
        switch ($sortType) {
            case 'view':
                $orderByFields = [
                    'view_count' => $sortWayType,
                    // 'updated_at'    => 'DESC',
                ];

                return $orderByFields;
                break;
            case 'like':
                $orderByFields = [
                    'like_count' => $sortWayType,
                    // 'updated_at'    => 'DESC',
                ];

                return $orderByFields;
                break;
            case 'follow':
                $orderByFields = [
                    'follow_count' => $sortWayType,
                    // 'updated_at'    => 'DESC',
                ];

                return $orderByFields;
                break;
            case 'shield':
                $orderByFields = [
                    'shield_count' => $sortWayType,
                    // 'updated_at'    => 'DESC',
                ];

                return $orderByFields;
                break;
            case 'post':
                $orderByFields = [
                    'post_count' => $sortWayType,
                    // 'updated_at'    => 'DESC',
                ];

                return $orderByFields;
                break;
            case 'essence':
                $orderByFields = [
                    'essence_count' => $sortWayType,
                    // 'updated_at'    => 'DESC',
                ];

                return $orderByFields;
                break;
            case 'time':
                $orderByFields = [
                    'created_at' => $sortWayType,
                    // 'updated_at'    => 'DESC',
                ];

                return $orderByFields;
                break;

            default:
                $orderByFields = [
                    'created_at' => $sortWayType,
                    // 'updated_at'    => 'DESC',
                ];

                return $orderByFields;
                break;
        }
    }
}
