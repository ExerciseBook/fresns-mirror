<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsExtends;

use App\Base\Models\BaseAdminModel;
use App\Http\Fresns\FresnsExtendLinkeds\FresnsExtendLinkedsConfig;
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
        $extendsTable = AmConfig::CFG_TABLE;
        // 屏蔽的目标字段
        $request = request();
        $mid = $request->header('mid');
        $query = DB::table("$extendsTable as extends")->select('extends.*')
            ->where('extends.deleted_at', null);
        // 公共参数
        // 搜索：关键词
        $searchKey = $request->input('searchKey');
        if ($searchKey) {
            $query->where('extends.title', 'like', "%{$searchKey}%");
        }
        // 搜索类型（搜索类型扩展配置的参数）
        $searchType = $request->input('searchType');
        if ($searchType) {
            $query->where('extends.extend_type', 'like', "%{$searchType}%");
        }
        // 指定范围：扩展内容
        $searchEid = $request->input('searchEid');
        if ($searchEid) {
            $query->where('extends.content', 'like', "%{$searchEid}%");
        }
        // 指定范围：成员
        $searchMid = $request->input('searchMid');
        if ($searchMid) {
            $query->where('extends.member_id', '=', $searchMid);
        }
        // 指定范围：帖子
        $searchPid = $request->input('searchPid');
        if ($searchPid) {
            $extendIdArr = Db::table(FresnsExtendLinkedsConfig::CFG_TABLE)->where('linked_type', 1)->where('linked_id',
                $searchPid)->pluck('extend_id')->toArray();
            $query->whereIn('extends.id', $searchMid);
        }
        // 指定范围：评论
        $searchCid = $request->input('searchCid');
        if ($searchCid) {
            $extendIdArr = Db::table(FresnsExtendLinkedsConfig::CFG_TABLE)->where('linked_type', 2)->where('linked_id',
                $searchCid)->pluck('extend_id')->toArray();
            $query->whereIn('extends.id', $searchMid);
        }
        // 排序处理
        $sortType = request()->input('sortType', '');
        $sortWay = request()->input('sortWay', 2);
        $sortWayType = $sortWay == 2 ? 'DESC' : 'ASC';
        switch ($sortType) {
            case 'created':
                $query->orderBy('extends.created_at', $sortWayType);
                break;
            case 'updated':
                $query->orderBy('extends.updated_at', $sortWayType);
                break;
            case 'rank_num':
                $query->orderBy('extends.rank_num', $sortWayType);
                break;
            default:
                $query->orderBy('extends.created_at', $sortWayType);
                break;
        }

        return $query;
    }
}
