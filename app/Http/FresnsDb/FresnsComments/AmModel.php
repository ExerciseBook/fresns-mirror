<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsComments;

use App\Base\Models\BaseAdminModel;
use App\Base\Models\BaseCategoryModel;
use App\Http\Center\AmGlobal\GlobalService;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsDb\FresnsCommentAppends\FresnsCommentAppendsConfig;
use App\Http\FresnsDb\FresnsMembers\FresnsMembers;
use App\Http\FresnsDb\FresnsMemberShields\FresnsMemberShieldsConfig;
use App\Http\FresnsDb\FresnsPosts\FresnsPosts;
use App\Http\FresnsDb\FresnsPosts\FresnsPostsConfig;
use Illuminate\Support\Facades\DB;

class AmModel extends BaseCategoryModel
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
        $commentTable = FresnsCommentsConfig::CFG_TABLE;
        $commentAppendTable = FresnsCommentAppendsConfig::CFG_TABLE;
        $postTable = FresnsPostsConfig::CFG_TABLE;
        /**
         * 过滤屏蔽对象的评论（成员、评论）。
         * searchType 留空代表输出所有内容。内容为插件 unikey 值，用于搜索包含指定插件扩展内容的帖子。
         * 默认排序类型「time」，默认排序方式「降序」.
         */
        // 屏蔽的目标字段
        $request = request();
        $mid = GlobalService::getGlobalKey('member_id');
        $memberShields = DB::table($memberShieldsTable)->where('member_id', $mid)->where('shield_type',
            1)->pluck('shield_id')->toArray();
        $commentShields = DB::table($memberShieldsTable)->where('member_id', $mid)->where('shield_type',
            5)->pluck('shield_id')->toArray();
        $query = DB::table("$commentTable as comment")->select('comment.*')
            ->join("$commentAppendTable as append", 'comment.id', '=', 'append.comment_id')
            ->whereNotIn('comment.member_id', $memberShields)
            ->whereNotIn('comment.id', $commentShields)
            ->where('comment.deleted_at', null);

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
                    $query->where('comment.member_id', '=', 0);
                }
                if ($site_private_end == 2) {
                    $query->where('comment.created_at', '<=', $memberInfo['expired_at']);
                }
            }
        }
        // 公共参数
        // 搜索：关键词
        $searchKey = $request->input('searchKey');
        if ($searchKey) {
            $query->where('append.content', 'like', "%{$searchKey}%");
        }
        // 搜索类型（搜索类型扩展配置的参数）
        $searchType = $request->input('searchType');
        if ($searchType) {
            $query->where('comment.type', 'like', "%{$searchType}%");
        }
        // 指定范围：成员
        $searchMid = $request->input('searchMid');
        if ($searchMid) {
            // 后台是否允许查看别人的评论
            $allowComment = ApiConfigHelper::getConfigByItemKey(AmConfig::IT_PUBLISH_COMMENTS) ?? false;
            $memberInfo = FresnsMembers::where('uuid', $searchMid)->first();

            // dd($allowPost)
            if (! $allowComment) {
                $query->where('comment.member_id', '=', 0);
            } else {
                if ($memberInfo) {
                    $query->where('comment.member_id', '=', $memberInfo['id']);
                } else {
                    $query->where('comment.member_id', '=', 0);
                }
            }
            // $query->where('comment.member_id','=',$searchMid);
        }
        // 指定范围：帖子
        $searchPid = $request->input('searchPid');
        if ($searchPid) {
            $posts = FresnsPosts::where('uuid', $searchPid)->first();
            // dd($posts);
            if ($posts) {
                $query->where('comment.post_id', '=', $posts['id']);
            } else {
                $query->where('comment.post_id', '=', 0);
            }
        }
        // 指定范围：评论
        $searchCid = $request->input('searchCid');
        if ($searchCid) {
            $comments = FresnsComments::where('uuid', $searchCid)->first();
            // 判断是否为一级评论
            // parent_id为0
            // dd($upComment);
            // dd($comments);
            // dd($data);
            if ($comments) {
                if ($comments['parent_id'] == 0) {
                    // $query->where('comment.id','=',$comments['id']);
                    $AmService = new AmService();
                    // dd($comments['id']);
                    request()->offsetSet('id', $comments['id']);
                    $data = $AmService->listTreeNoRankNum();
                    $data = $AmService->treeData();
                    // dd($data);
                    // 获取childrenIdArr
                    if ($data) {
                        $childrenIdArr = [];
                        foreach ($data as $v) {
                            $this->getChildrenIds($v, $childrenIdArr);
                        }
                        // dd($childrenIdArr);
                    }
                    array_unshift($childrenIdArr, $comments['id']);
                    // dd($childrenIdArr);
                    request()->offsetUnset('id');
                    // dd($childrenIdArr);
                    // $query->where('comment.id','=',$comments['id']);
                    $query->whereIn('comment.id', $childrenIdArr)->where('comment.parent_id', '!=', 0);
                } else {
                    $query->where('comment.id', '=', 0);
                }
            } else {
                $query->where('comment.id', '=', 0);
            }
        } else {
            $query->where('comment.parent_id', '=', 0);
        }
        // 置顶
        $searchSticky = $request->input('searchSticky');
        // dump($searchSticky);
        // dd(!empty($searchSticky));
        if (! empty($searchSticky)) {
            // $searchEssenceType = $searchEssence == false ? 0 : 1;
            $query->where('comment.is_sticky', '=', $searchSticky);
        }
        if ($searchSticky == '0') {
            $query->where('comment.is_sticky', '=', 0);
        }
        // likeCountGt
        $likeCountGt = $request->input('likeCountGt');
        if ($likeCountGt) {
            $query->where('comment.like_count', '>=', $likeCountGt);
        }
        // likeCountLt
        $likeCountLt = $request->input('likeCountLt');
        if ($likeCountLt) {
            $query->where('comment.like_count', '<=', $likeCountLt);
        }
        // followCountGt
        $followCountGt = $request->input('followCountGt');
        if ($followCountGt) {
            $query->where('comment.follow_count', '>=', $followCountGt);
        }
        // followCountLt
        $followCountLt = $request->input('followCountLt');
        if ($followCountLt) {
            $query->where('comment.follow_count', '<=', $followCountLt);
        }
        // shieldCountGt
        $shieldCountGt = $request->input('shieldCountGt');
        if ($shieldCountGt) {
            $query->where('comment.shield_count', '>=', $shieldCountGt);
        }
        // shield_count
        $shieldCountLt = $request->input('shieldCountLt');
        if ($shieldCountLt) {
            $query->where('comment.shield_count', '<=', $shieldCountLt);
        }
        // commentCountGt
        $commentCountGt = $request->input('commentCountGt');
        if ($commentCountGt) {
            $query->where('comment.comment_count', '>=', $commentCountGt);
        }
        // commentCountLt
        $commentCountLt = $request->input('commentCountLt');
        if ($commentCountLt) {
            $query->where('comment.comment_count', '<=', $commentCountLt);
        }
        // createdTimeGt
        $createdTimeGt = $request->input('createdTimeGt');
        if ($createdTimeGt) {
            $query->where('comment.created_at', '>=', $createdTimeGt);
        }
        // createdTimeLt
        $createdTimeLt = $request->input('createdTimeLt');
        if ($createdTimeLt) {
            $query->where('comment.created_at', '<=', $createdTimeLt);
        }
        // publishTimeGt
        $publishTimeGt = $request->input('publishTimeGt');
        if ($publishTimeGt) {
            $query->where('comment.created_at', '>=', $publishTimeGt);
        }
        // publishTimeLt
        $publishTimeLt = $request->input('publishTimeLt');
        if ($publishTimeLt) {
            $query->where('comment.created_at', '<=', $publishTimeLt);
        }
        // 排序处理
        $sortType = request()->input('sortType', '');
        $sortWay = request()->input('sortDirection', 2);
        $sortWayType = $sortWay == 2 ? 'DESC' : 'ASC';
        switch ($sortType) {
            case 'view':
                $query->orderBy('comment.view_count', $sortWayType);
                break;
            case 'follow':
                $query->orderBy('comment.follow_count', $sortWayType);
                break;
            case 'shield':
                $query->orderBy('comment.shield_count', $sortWayType);
                break;
            case 'comment ':
                $query->orderBy('comment.comment_count', $sortWayType);
                break;
            case 'time':
                $query->orderBy('comment.created_at', $sortWayType);
                break;
            default:
                $query->orderBy('comment.created_at', $sortWayType);
                break;
        }
        // dd($query);
        return $query;
    }

    // 搜索排序字段
    public function initOrderByFields()
    {
        $orderByFields = [
            'created_at' => 'DESC',
            // 'updated_at'    => 'DESC',
        ];

        return $orderByFields;
    }

    // 获取childrenIds
    public function getChildrenIds($categoryItem, &$childrenIdArr)
    {
        // dd($categoryItem);
        if (key_exists('children', $categoryItem)) {
            $childrenArr = $categoryItem['children'];
            // dd($childrenArr);
            foreach ($childrenArr as $children) {
                $childrenIdArr[] = $children['value'];
                $this->getChildrenIds($children, $childrenIdArr);
            }
        }
        // dd($childrenIdArr);
    }
}
