<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsEmojis;

use App\Base\Services\BaseAdminService;
use App\Plugins\Tweet\TweetConfigs\TweetConfigService;

class AmService extends BaseAdminService
{
    protected $needCommon = false;

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
        $languageArr = TweetConfigService::getLanguageStatus();
        $common['languagesOption'] = $languageArr['languagesOption'];
        return $common;
    }

    public function update($id)
    {
        parent::update($id);
        $this->model->hookUpdateAfter($id);
    }

}