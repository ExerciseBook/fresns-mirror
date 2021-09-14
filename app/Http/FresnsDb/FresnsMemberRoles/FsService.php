<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsMemberRoles;

use App\Base\Services\BaseAdminService;

class FsService extends BaseAdminService
{
    protected $needCommon = false;

    public function __construct()
    {
        $this->model = new FsModel();
        $this->resource = FsResource::class;
        $this->resourceDetail = FsResourceDetail::class;
    }

    // Get permission for map
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
