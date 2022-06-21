<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Controllers;

use App\Exceptions\ApiException;
use App\Utilities\ConfigUtility;
use Illuminate\Http\Request;

class EditorController extends Controller
{
    // config
    public function config($type)
    {
        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUser = $this->user();

        switch ($type) {
            // post
            case 'post':
                $config['editor'] = ConfigUtility::getEditorConfigByType($authUser->id, 'post', $langTag);
                $config['publish'] = ConfigUtility::getPublishConfigByType($authUser->id, 'post', $langTag, $timezone);
                $config['edit'] = ConfigUtility::getEditConfigByType('post');
            break;

            // comment
            case 'comment':
                $config['editor'] = ConfigUtility::getEditorConfigByType($authUser->id, 'comment', $langTag);
                $config['publish'] = ConfigUtility::getPublishConfigByType($authUser->id, 'comment', $langTag, $timezone);
                $config['edit'] = ConfigUtility::getEditConfigByType('comment');
            break;
        }

        return $this->success($config);
    }

    // postCreate
    public function postCreate(Request $request)
    {
        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUser = $this->user();

        return $this->success();
    }
}
