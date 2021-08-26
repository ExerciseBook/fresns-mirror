<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Base\Models;

use App\Base\Config\BaseConfig;
use App\Helpers\CommonHelper;
use App\Helpers\TreeHelper;
use App\Traits\QueryTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// 场景
// 场景1：查询c1的所有祖先类目
// 场景2：查询c1的所有后代类目
// 场景3：判断c1 和 c2 是否有层级关系
class BaseCategoryModel extends BaseModel
{
    public $pageSize = BaseConfig::DEFAULT_ALL_IN_ONE_PAGE_SIZE;

    // 插入完成之后做的操作
    public function hookStoreAfter($id)
    {
        $currModel = get_class($this);
        $category = (new $currModel)->find($id);

        if (empty($category->parent_id)) { // 创建的是根目录
            $category->level = 0; // 将层级设为0
            $category->path = '-'; // 将 path 设为 -
        } else {
            // 创建的并非根目录
            $category->level = $category->parent->level + 1; // 将层级设为父类层级+1
            $category->path = $category->parent->path.$category->parent_id.'-'; // 将path值设为父类path+父类id
        }

        $category->save();

        return $id;
    }

    // 获取上层父节点
    public function parent()
    {
        return $this->belongsTo(get_called_class());
    }

    // 获取一级子节点
    public function children()
    {
        return $this->hasMany(get_called_class(), 'parent_id');
    }

    // 获取所有子节点
    public function getAllChildren()
    {
        $result = [];
        $children = $this->children;

        foreach ($children as $child) {
            $child->key = $child->id;
            $child->value = $child->id;
            $child->title = $child->name;
            $result[] = $child;

            $childResult = $child->getAllChildren();
            foreach ($childResult as $subChild) {
                $result[] = $subChild;
            }
        }

        return $result;
    }

    /**
     * 获取所有祖先分类id.
     * @date 2019-04-21
     */
    public function getPathIdsAttribute()
    {
        $path = trim($this->path, '-'); // 过滤两端的 -
        $path = explode('-', $path); // 以 - 为分隔符切割为数组
        $path = array_filter($path); // 过滤空值元素

        return $path;
    }

    /**
     * 获取所有祖先分类且按层级正序排列.
     * @date 2019-04-21
     */
    public function getAncestorsAttribute()
    {
        return BaseCategoryModel::query()
            ->whereIn('id', $this->path_ids) // 调用 getPathIdsAttribute 获取祖先类目id
            ->orderBy('level') // 按层级排列
            ->get();
    }

    /**
     * 获取所有祖先类目名称以及当前类目的名称.
     * @date 2019-04-21
     */
    public function getFullNameAttribute()
    {
        return $this->ancestors // 调用 getAncestorsAttribute 获取祖先类目
        ->pluck('name') // 将所有祖先类目的 name 字段作为一个数组
        ->push($this->name) // 追加当前类目的name字段到数组末尾
        ->implode(' - '); // 用 - 符号将数组的值组装成一个字符串
    }

    // 获取childrenIds
    public function getAllChildrenIds(&$childrenIdArr)
    {
        $children = $this->getAllChildren();
        foreach ($children as $child) {
            $childrenIdArr[] = $child->id;
            if (! empty($child->children)) {
                $this->getAllChildrenIds($childrenIdArr);
            }
        }
    }

    // 删除之前的操作, 找到每个分类的子类，然后进行删除
    public function hookDestroyBefore($idArr)
    {
        $currClass = get_called_class();
        $currModel = new $currClass;

        // 先要获取所有待删除的 id
        $allChildrenIds = [];
        foreach ($idArr as $id) {
            $category = $currModel->find($id);
            $allChildren = $category->getAllChildren();

            //   dd($allChildren);

            $allChildrenArr = CommonHelper::objectToArray($allChildren);
            TreeHelper::getAllIdsInTreeData($allChildrenArr, $allChildrenIds);
        }

        $allNeedDestroyIdArr = array_unique(array_merge($idArr, $allChildrenIds));

        // 逐个删除
        foreach ($allNeedDestroyIdArr as $id) {
            $this->hookDestroyItemBefore($id);

            // 执行删除
            $currModel->find($id)->delete();
        }
    }

    // 是否可以删除
    public function canDelete($idArr)
    {
        return parent::canDelete($idArr);
    }
}
