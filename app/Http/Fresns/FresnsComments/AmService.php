<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsComments;

use App\Base\Services\BaseAdminService;
use App\Base\Services\BaseCategoryService;

class AmService extends BaseCategoryService
{
    public function __construct()
    {
        $this->config = new AmConfig();
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

}