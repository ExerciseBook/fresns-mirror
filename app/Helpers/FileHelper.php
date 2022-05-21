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
    public static function fresnsFileStorageConfigByType(int $type)
    {
        $key = match ($type) {
            1 => 'image',
            2 => 'video',
            3 => 'audio',
            4 => 'document',
            default => 'image',
        };

        $data = ConfigHelper::fresnsConfigByItemKeys([
            "{$key}_service",
            "{$key}_secret_id",
            "{$key}_secret_key",
            "{$key}_bucket_name",
            "{$key}_bucket_area",
            "{$key}_bucket_domain",
            "{$key}_url_status",
            "{$key}_url_key",
            "{$key}_url_expire",
        ]);

        $config = [
            'service' => $data["{$key}_service"],
            'secretId' => $data["{$key}_secret_id"],
            'secretKey' => $data["{$key}_secret_key"],
            'bucketName' => $data["{$key}_bucket_name"],
            'bucketArea' => $data["{$key}_bucket_area"],
            'bucketDomain' => $data["{$key}_bucket_domain"],
            'antiLinkStatus' => $data["{$key}_url_status"],
            'antiLinkKey' => $data["{$key}_url_key"],
            'antiLinkExpire' => $data["{$key}_url_expire"],
        ];

        return $config;
    }

    public static function fresnsFileStorageConfigStatusByType(int $type)
    {
        $config = FileHelper::fresnsFileStorageConfigByType($type);

        if (empty($config['service']) || empty($config['secretId']) || empty($config['secretKey']) || empty($config['bucketName']) || empty($config['bucketDomain'])) {
            return false;
        }

        return true;
    }

    public static function fresnsFileAntiLinkStatusByType(int $type)
    {
        $config = FileHelper::fresnsFileStorageConfigByType($type);

        if (! $config['antiLinkStatus']) {
            return false;
        }

        if (empty($config['service']) || empty($config['antiLinkKey']) || empty($config['antiLinkExpire'])) {
            return false;
        }

        return true;
    }

    public static function fresnsFileInfoById(int $fileId)
    {
        $file = File::whereId($fileId)->firstOrFail();

        return $file->getFileInfo();
    }

    public static function fresnsFileInfoByFid(string $fid)
    {
        $file = File::whereFid($fid)->firstOrFail();

        return $file->getFileInfo();
    }

    public static function fresnsFileImageUrlByColumn(?int $fileId = null, ?string $fileUrl = null, ?string $urlType = null)
    {
        if (! $fileId && ! $fileUrl ) {
            return null;
        }

        if (! $fileId) {
            return $fileUrl;
        }

        $urlType = $urlType ?: 'imageConfigUrl';

        if (FileHelper::fresnsFileAntiLinkStatusByType(1)) {
            $fresnsResponse = \FresnsCmdWord::plugin()->getFileInfo([
                'fileId' => $fileId,
            ]);

            return $fresnsResponse->getData($urlType) ?? null;
        }

        $file = File::whereId($fileId)->firstOrFail();

        return $file->getFileInfo()[$urlType] ?? null;
    }
}
