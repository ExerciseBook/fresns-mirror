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
    // get file storage config by type
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
            "{$key}_filesystem_disk",
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
            'filesystemDisk' => $data["{$key}_filesystem_disk"],
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

    // get file storage path
    public static function fresnsFileStoragePath(int $fileType, int $useType)
    {
        $fileTypeDir = match ($fileType) {
            1 => 'images',
            2 => 'videos',
            3 => 'audios',
            4 => 'documents',
        };

        $useTypeDir = match ($useType) {
            1 => '/mores/{YYYYMM}/',
            2 => '/systems/{YYYYMM}/',
            3 => '/operations/{YYYYMM}/',
            4 => '/stickers/{YYYYMM}/',
            5 => '/avatars/{YYYYMM}/{DD}/',
            6 => '/dialogs/{YYYYMM}/{DD}/',
            7 => '/posts/{YYYYMM}/{DD}/',
            8 => '/comments/{YYYYMM}/{DD}/',
            9 => '/extends/{YYYYMM}/{DD}/',
            10 => '/plugins/{YYYYMM}/{DD}/',
        };

        $replaceUseTypeDir = str_replace(
            ['{YYYYMM}', '{DD}'],
            [date('Ym'), date('d')],
            $useTypeDir
        );

        return sprintf('%s/%s', trim($fileTypeDir, '/'), trim($replaceUseTypeDir, '/'));
    }

    // get file info by file id or fid
    public static function fresnsFileInfoById(string $fileIdOrFid)
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
            $fresnsResponse = \FresnsCmdWord::plugin($storageConfig['service'])->getAntiLinkFileInfo([
                'type' => $file->type,
                'fileIdOrFid' => $file->id,
            ]);

            return $fresnsResponse->getData() ?? null;
        }

        return $file->getFileInfo();
    }

    // get file info list by file id or fid
    public static function fresnsFileInfoListByIds(array $fileIdsOrFids)
    {
        $fileQuery = File::whereIn('id', $fileIdsOrFids)->orWhereIn('fid', $fileIdsOrFids)->groupBy('type')->get();

        $files['images'] = $fileQuery->get(File::TYPE_IMAGE)?->all() ?? null;
        $files['videos'] = $fileQuery->get(File::TYPE_VIDEO)?->all() ?? null;
        $files['audios'] = $fileQuery->get(File::TYPE_AUDIO)?->all() ?? null;
        $files['documents'] = $fileQuery->get(File::TYPE_DOCUMENT)?->all() ?? null;

        return $files;
    }

    // get file info list by table column
    public static function fresnsFileInfoListByTableColumn(string $tableName, string $tableColumn, ?int $tableId = null, ?string $tableKey = null)
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

        $files['images'] = $fileList->get(File::TYPE_IMAGE)?->all() ?? null;
        $files['videos'] = $fileList->get(File::TYPE_VIDEO)?->all() ?? null;
        $files['audios'] = $fileList->get(File::TYPE_AUDIO)?->all() ?? null;
        $files['documents'] = $fileList->get(File::TYPE_DOCUMENT)?->all() ?? null;

        return $files;
    }

    // get file url by table column
    public static function fresnsFileUrlByTableColumn(?int $idColumn = null, ?string $urlColumn = null, ?string $urlType = null)
    {
        if (! $idColumn && ! $urlColumn) {
            return null;
        }

        if (! $idColumn) {
            return $urlColumn;
        }

        $file = File::whereId($idColumn)->first();
        if (empty($file)) {
            return null;
        }

        $urlType = $urlType ?: 'imageConfigUrl';

        $antiLinkConfigStatus = FileHelper::fresnsFileStorageConfigByType($file->type)['antiLinkConfigStatus'];

        if ($antiLinkConfigStatus) {
            $fresnsResponse = \FresnsCmdWord::plugin()->getAntiLinkFileInfo([
                'type' => $file->type,
                'fileIdOrFid' => $file->id,
            ]);

            return $fresnsResponse->getData($urlType) ?? null;
        }

        return $file->getFileInfo()[$urlType] ?? null;
    }

    // get anti link file info list
    public static function fresnsAntiLinkFileInfoListByIds(array $fileIdsOrFids)
    {
        $files = FileHelper::fresnsFileInfoListByIds($fileIdsOrFids);

        $fileList = FileHelper::handleAntiLinkFileInfoList($files);

        return $fileList;
    }

    // get anti link file info list by table column
    public static function fresnsAntiLinkFileInfoListByTableColumn(string $tableName, string $tableColumn, ?int $tableId = null, ?string $tableKey = null)
    {
        $files = FileHelper::fresnsFileInfoListByTableColumn($tableName, $tableColumn, $tableId, $tableKey);

        $fileList = FileHelper::handleAntiLinkFileInfoList($files);

        return $fileList;
    }

    // get file original url by file id or fid
    public static function fresnsFileOriginalUrlById(string $fileIdOrFid)
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
            $fresnsResponse = \FresnsCmdWord::plugin($storageConfig['service'])->getAntiLinkFileOriginalUrl([
                'type' => $file->type,
                'fileIdOrFid' => $file->id,
            ]);

            return $fresnsResponse->getData('originalUrl') ?? null;
        }

        return $file->getFileOriginalUrl();
    }

    public static function handleAntiLinkFileInfoList(array $files)
    {
        $imageStorageConfig = FileHelper::fresnsFileStorageConfigByType(File::TYPE_IMAGE);
        $videoStorageConfig = FileHelper::fresnsFileStorageConfigByType(File::TYPE_VIDEO);
        $audioStorageConfig = FileHelper::fresnsFileStorageConfigByType(File::TYPE_AUDIO);
        $documentStorageConfig = FileHelper::fresnsFileStorageConfigByType(File::TYPE_DOCUMENT);

        // image
        if ($imageStorageConfig['antiLinkConfigStatus'] && empty($files['images'])) {
            $fids = array_column($files['images'], 'fid');

            $fresnsResponse = \FresnsCmdWord::plugin($imageStorageConfig['service'])->getAntiLinkFileInfoList([
                'type' => 1,
                'fileIdsOrFids' => $fids,
            ]);

            $files['images'] = $fresnsResponse->getData();
        }

        // video
        if ($videoStorageConfig['antiLinkConfigStatus'] && empty($files['videos'])) {
            $fids = array_column($files['videos'], 'fid');

            $fresnsResponse = \FresnsCmdWord::plugin($videoStorageConfig['service'])->getAntiLinkFileInfoList([
                'type' => 2,
                'fileIdsOrFids' => $fids,
            ]);

            $files['videos'] = $fresnsResponse->getData();
        }

        // audio
        if ($audioStorageConfig['antiLinkConfigStatus'] && empty($files['audios'])) {
            $fids = array_column($files['audios'], 'fid');

            $fresnsResponse = \FresnsCmdWord::plugin($audioStorageConfig['service'])->getAntiLinkFileInfoList([
                'type' => 3,
                'fileIdsOrFids' => $fids,
            ]);

            $files['audios'] = $fresnsResponse->getData();
        }

        // document
        if ($documentStorageConfig['antiLinkConfigStatus'] && empty($files['documents'])) {
            $fids = array_column($files['documents'], 'fid');

            $fresnsResponse = \FresnsCmdWord::plugin($documentStorageConfig['service'])->getAntiLinkFileInfoList([
                'type' => 4,
                'fileIdsOrFids' => $fids,
            ]);

            $files['documents'] = $fresnsResponse->getData();
        }

        return $files;
    }
}
