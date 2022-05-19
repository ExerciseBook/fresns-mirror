<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Controllers;

use App\Helpers\AppHelper;
use App\Helpers\InteractiveHelper;
use App\Models\Hashtag;
use App\Models\Seo;
use App\Exceptions\FresnsApiException;
use Illuminate\Http\Request;

class HashtagController extends Controller
{
    public function detail(string $hid)
    {
        $headers = AppHelper::getApiHeaders();

        $hashtag = Hashtag::whereSlug($hid)->first();
        if (empty($hashtag)) {
            throw new FresnsApiException(37200);
        }

        $seoData = Seo::where('linked_type', 3)->where('linked_id', $hashtag->id)->where('lang_tag', $headers['langTag'])->first();
        $common['title'] = $seoData->title ?? null;
        $common['keywords'] = $seoData->keywords ?? null;
        $common['description'] = $seoData->description ?? null;
        $data['commons'] = $common;

        $hashtagInfo = $hashtag->getHashtagInfo($headers['langTag']);
        $hashtagInteractive = InteractiveHelper::fresnsHashtagInteractive($headers['langTag']);

        $data['detail'] = array_merge($hashtagInfo, $hashtagInteractive);

        return $this->success($data);
    }
}
