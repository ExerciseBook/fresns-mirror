<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use App\Models\File;

class FileHelper
{
    public static function fresnsFileUrlById(int $fileId)
    {
        $file = File::idOrFid(['id' => $fileId])->firstOrFail();

        $fileInfo = $file->getFileInfo();

        return collect($fileInfo)->only([
            'type',
            'imageDefaultUrl',
            'imageConfigUrl',
            'imageAvatarUrl',
            'imageRatioUrl',
            'imageSquareUrl',
            'imageBigUrl',
            'imageOriginalUrl',
            'videoCover',
            'videoGif',
            'videoUrl',
            'videoOriginalUrl',
            'audioUrl',
            'audioOriginalUrl',
            'documentUrl',
            'documentOriginalUrl',
        ]);
    }

    public static function fresnsFileUrlByFid(string $fid)
    {
        $file = File::idOrFid(['fid' => $fid])->firstOrFail();

        $fileInfo = $file->getFileInfo();

        return collect($fileInfo)->only([
            'type',
            'imageDefaultUrl',
            'imageConfigUrl',
            'imageAvatarUrl',
            'imageRatioUrl',
            'imageSquareUrl',
            'imageBigUrl',
            'imageOriginalUrl',
            'videoCover',
            'videoGif',
            'videoUrl',
            'videoOriginalUrl',
            'audioUrl',
            'audioOriginalUrl',
            'documentUrl',
            'documentOriginalUrl',
        ]);
    }

    public static function fresnsFileInfoById(int $fileId)
    {
        $file = File::idOrFid(['id' => $fileId])->firstOrFail();

        $fileInfo = $file->getFileInfo();

        return $fileInfo;
    }

    public static function fresnsFileInfoByFid(string $fid)
    {
        $file = File::idOrFid(['fid' => $fid])->firstOrFail();

        $fileInfo = $file->getFileInfo();

        return $fileInfo;
    }

    public static function fresnsFileImageUrlByColumn($fileId, $fileUrl, $urlType)
    {
        if (! $fileId) {
            return $fileUrl;
        }

        if (! File::isEnableAntiTheftChainOfFileType(File::TYPE_IMAGE)) {
            return $fileUrl;
        }

        $fresnsResponse = \FresnsCmdWord::plugin()->getFileUrlOfAntiLink([
            'fileId' => $fileId,
        ]);

        return $fresnsResponse->getData($urlType) ?? null;
    }

    // icon file
    public static function fresnsFileIconsByArray(array $icons)
    {
        $iconList = [];

        foreach ($icons as $icon) {
            $key = $icon['name'];
            $image = FileHelper::fresnsFileImageUrlByColumn($icon['fileId'], $icon['fileUrl'], 'imageConfigUrl');

            $iconList[$key] = $image;
        }

        return $iconList;
    }
}
