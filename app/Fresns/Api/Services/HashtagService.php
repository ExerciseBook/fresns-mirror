<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Services;

use App\Helpers\InteractiveHelper;
use App\Models\ArchiveUsage;
use App\Models\ExtendUsage;
use App\Models\Hashtag;
use App\Models\OperationUsage;
use App\Utilities\ExtendUtility;
use App\Utilities\InteractiveUtility;

class HashtagService
{
    public function hashtagData(?Hashtag $hashtag, string $langTag, ?int $authUserId = null)
    {
        if (! $hashtag) {
            return null;
        }

        $hashtagInfo = $hashtag->getHashtagInfo($langTag);

        $item['archives'] = ExtendUtility::getArchives(ArchiveUsage::TYPE_HASHTAG, $hashtag->id, $langTag);
        $item['operations'] = ExtendUtility::getOperations(OperationUsage::TYPE_HASHTAG, $hashtag->id, $langTag);
        $item['extends'] = ExtendUtility::getExtends(ExtendUsage::TYPE_HASHTAG, $hashtag->id, $langTag);

        $interactiveConfig = InteractiveHelper::fresnsHashtagInteractive($langTag);
        $interactiveStatus = InteractiveUtility::getInteractiveStatus(InteractiveUtility::TYPE_HASHTAG, $hashtag->id, $authUserId);
        $item['interactive'] = array_merge($interactiveConfig, $interactiveStatus);

        $data = array_merge($hashtagInfo, $item);

        return $data;
    }
}
