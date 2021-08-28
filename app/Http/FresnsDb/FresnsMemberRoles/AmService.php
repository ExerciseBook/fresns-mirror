<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsMemberRoles;

use App\Base\Services\BaseAdminService;

class AmService extends BaseAdminService
{
    protected $needCommon = false;

    public function __construct()
    {
        $this->model = new AmModel();
        $this->resource = AmResource::class;
        $this->resourceDetail = AmResourceDetail::class;
    }

    //获取权限的map
    public static function getPermissionMap($permissionArr)
    {
        $permissionMap = [];
        foreach ($permissionArr as $v) {
            if (empty($v['permKey']) || ! isset($v['permValue'])) {
                return [];
                break;
            }
            $permissionMap[$v['permKey']] = $v['permValue'];
        }

        return $permissionMap;
    }
}
