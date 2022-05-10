<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models\Traits;

use App\Helpers\FileHelper;
use App\Helpers\LanguageHelper;

trait HashtagServiceTrait
{
    public function getHashtagInfo(string $langTag = '')
    {
        $hashtagData = $this;

        $info['huri'] = $hashtagData->slug;
        $info['hname'] = $hashtagData->name;
        $info['cover'] = FileHelper::fresnsFileImageUrlByColumn($hashtagData->cover_file_id, $hashtagData->cover_file_url, 'imageConfigUrl');
        $info['description'] = LanguageHelper::fresnsLanguageByTableId('hashtags', 'description', $hashtagData->id, $langTag);
        $info['viewCount'] = $hashtagData->view_count;
        $info['likeCount'] = $hashtagData->like_count;
        $info['followCount'] = $hashtagData->follow_count;
        $info['blockCount'] = $hashtagData->block_count;
        $info['postCount'] = $hashtagData->post_count;
        $info['digestCount'] = $hashtagData->digest_count;

        return $info;
    }
}
