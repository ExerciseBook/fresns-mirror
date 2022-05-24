<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use App\Models\File;
use App\Models\FileAppend;

class FileHelper
{
    public static function fresnsFileStorageConfigByType(int $type)
    {
        $key = match ($type) {
            1 => 'image',
            2 => 'video',
            3 => 'audio',
            4 => 'document',
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

        $config['storageConfigStatus'] = true;
        if (empty($config['service']) || empty($config['secretId']) || empty($config['secretKey']) || empty($config['bucketName']) || empty($config['bucketDomain'])) {
            $config['storageConfigStatus'] = false;
        }

        $config['antiLinkConfigStatus'] = true;
        if (! $config['antiLinkStatus']) {
            $config['antiLinkConfigStatus'] = false;
        }
        if (empty($config['service']) || empty($config['antiLinkKey']) || empty($config['antiLinkExpire'])) {
            $config['antiLinkConfigStatus'] = false;
        }

        return $config;
    }

    // get file info by id or fid
    public static function fresnsFileInfo(string $fileIdOrFid)
    {
        if (is_numeric($fileIdOrFid)) {
            $file = File::whereId($fileIdOrFid)->first();
        } else {
            $file = File::whereFid($fileIdOrFid)->first();
        }

        if (empty($file)) {
            return null;
        }

        $storageConfig = FileHelper::fresnsFileStorageConfigByType($file->type);

        if ($storageConfig['antiLinkConfigStatus']) {
            switch ($file->type) {
                // Image
                case 1:
                    $fresnsResponse = \FresnsCmdWord::plugin($storageConfig['service'])->getAntiLinkFileInfoForImage([
                        'fileId' => $file->id,
                    ]);
                break;

                // Video
                case 2:
                    $fresnsResponse = \FresnsCmdWord::plugin($storageConfig['service'])->getAntiLinkFileInfoForVideo([
                        'fileId' => $file->id,
                    ]);
                break;

                // Audio
                case 3:
                    $fresnsResponse = \FresnsCmdWord::plugin($storageConfig['service'])->getAntiLinkFileInfoForAudio([
                        'fileId' => $file->id,
                    ]);
                break;

                // Document
                case 4:
                    $fresnsResponse = \FresnsCmdWord::plugin($storageConfig['service'])->getAntiLinkFileInfoForDocument([
                        'fileId' => $file->id,
                    ]);
                break;
            }

            return $fresnsResponse->getData() ?? null;
        }

        return $file->getFileInfo();
    }

    public static function fresnsFileInfoListByTable(string $tableName, string $tableColumn, ?int $tableId = null, ?string $tableKey = null)
    {
        $fileAppendQuery =  FileAppend::with('file')
            ->where('table_name', $tableName)
            ->where('table_column', $tableColumn)
            ->orderBy('rating');

        if (empty($tableId)) {
            $fileAppendQuery->where('table_key', $tableKey);
        } else{
            $fileAppendQuery->where('table_id', $tableId);
        }

        $fileAppends = $fileAppendQuery->get();

        $fileList = $fileAppends->map(fn ($fileAppend) => $fileAppend->file->getFileInfo())->groupBy('type');

        $files['images'] = $fileList->get(1)?->all() ?? null;
        $files['videos'] = $fileList->get(2)?->all() ?? null;
        $files['audios'] = $fileList->get(3)?->all() ?? null;
        $files['documents'] = $fileList->get(4)?->all() ?? null;

        $imageStorageConfig = FileHelper::fresnsFileStorageConfigByType(1);
        $videoStorageConfig = FileHelper::fresnsFileStorageConfigByType(2);
        $audioStorageConfig = FileHelper::fresnsFileStorageConfigByType(3);
        $documentStorageConfig = FileHelper::fresnsFileStorageConfigByType(4);

        if ($imageStorageConfig['antiLinkConfigStatus'] && empty($files['images'])) {
            $fids = $files['images']->pluck('fid')->get();

            $fresnsResponse = \FresnsCmdWord::plugin($imageStorageConfig['service'])->getAntiLinkFileInfoForImageList([
                'fids' => $fids,
            ]);

            $files['images'] = $fresnsResponse->getData();
        }

        if ($videoStorageConfig['antiLinkConfigStatus'] && empty($files['videos'])) {
            $fids = $files['videos']->pluck('fid')->get();

            $fresnsResponse = \FresnsCmdWord::plugin($videoStorageConfig['service'])->getAntiLinkFileInfoForVideoList([
                'fids' => $fids,
            ]);

            $files['videos'] = $fresnsResponse->getData();
        }

        if ($audioStorageConfig['antiLinkConfigStatus'] && empty($files['audios'])) {
            $fids = $files['audios']->pluck('fid')->get();

            $fresnsResponse = \FresnsCmdWord::plugin($audioStorageConfig['service'])->getAntiLinkFileInfoForAudioList([
                'fids' => $fids,
            ]);

            $files['audios'] = $fresnsResponse->getData();
        }

        if ($documentStorageConfig['antiLinkConfigStatus'] && empty($files['documents'])) {
            $fids = $files['documents']->pluck('fid')->get();

            $fresnsResponse = \FresnsCmdWord::plugin($documentStorageConfig['service'])->getAntiLinkFileInfoForDocumentList([
                'fids' => $fids,
            ]);

            $files['documents'] = $fresnsResponse->getData();
        }

        return $files ?? null;
    }

    public static function fresnsFileImageUrlByColumn(?int $fileId = null, ?string $fileUrl = null, ?string $urlType = null)
    {
        if (! $fileId && ! $fileUrl) {
            return null;
        }

        if (! $fileId) {
            return $fileUrl;
        }

        $urlType = $urlType ?: 'imageConfigUrl';

        $antiLinkConfigStatus = FileHelper::fresnsFileStorageConfigByType(1)['antiLinkConfigStatus'];

        if ($antiLinkConfigStatus) {
            $fresnsResponse = \FresnsCmdWord::plugin()->getFileInfo([
                'fileId' => $fileId,
            ]);

            return $fresnsResponse->getData($urlType) ?? null;
        }

        $file = File::whereId($fileId)->first();
        if (empty($file)) {
            return null;
        }

        return $file->getFileInfo()[$urlType] ?? null;
    }
}
