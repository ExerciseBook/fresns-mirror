<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Utilities;

use App\Helpers\FileHelper;
use App\Helpers\PrimaryHelper;
use App\Models\File;
use App\Models\FileUsage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUtility
{
    public static function uploadFile(array $bodyInfo, UploadedFile $file)
    {
        if (! Str::isJson($bodyInfo['moreJson'])) {
            return null;
        }

        $fresnsStorage = Storage::build([
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => '/storage',
        ]);

        $storePath = FileHelper::fresnsFileStoragePath($bodyInfo['type'], $bodyInfo['usageType']);

        $diskPath = $fresnsStorage->putFile($storePath, $file);

        // $filepath = storage_path('app/public/'.$diskPath);

        return FileUtility::saveFileInfoToDatabase($bodyInfo, $diskPath, $file);
    }

    public static function uploadFileInfo(array $bodyInfo)
    {
        if (! Str::isJson($bodyInfo['fileInfo'])) {
            return null;
        }

        $fileIdArr = [];
        foreach ($bodyInfo['fileInfo'] as $fileInfo) {
            $imageIsLong = 0;
            if ($fileInfo['type'] == 1 && ! empty($fileInfo['imageWidth']) >= 700) {
                if ($fileInfo['imageHeight'] >= $fileInfo['imageWidth'] * 3) {
                    $imageIsLong = 1;
                }
            }

            $fileInput = [
                'fid' => Str::random(12),
                'type' => $bodyInfo['type'], // bodyInfo
                'name' => $fileInfo['name'],
                'mime' => $fileInfo['mime'] ?? null,
                'extension' => $fileInfo['extension'],
                'size' => $fileInfo['size'],
                'md5' => $fileInfo['md5'] ?? null,
                'sha' => $fileInfo['sha'] ?? null,
                'sha_type' =>  $fileInfo['shaType'] ?? null,
                'path' => $fileInfo['path'],
                'image_width' => $fileInfo['imageWidth'] ?? null,
                'image_height' => $fileInfo['imageHeight'] ?? null,
                'image_is_long' => $imageIsLong,
                'video_time' => $fileInfo['videoTime'] ?? null,
                'video_cover_path' => $fileInfo['videoCoverPath'] ?? null,
                'video_gif_path' => $fileInfo['videoGifPath'] ?? null,
                'audio_time' => $fileInfo['audioTime'] ?? null,
                'more_json' => $fileInfo['moreJson'],
                'original_path' => $fileInfo['originalPath'] ?? null,
            ];
            $fileId = File::create($fileInput)->id;

            $fileIdArr[] = $fileId;

            $accountId = PrimaryHelper::fresnsAccountIdByAid($bodyInfo['aid']);
            $userId = PrimaryHelper::fresnsUserIdByUidOrUsername($bodyInfo['uid']);

            $tableId = $bodyInfo['tableId'];
            if (empty($bodyInfo['tableId'])) {
                $tableId = PrimaryHelper::fresnsPrimaryId($bodyInfo['tableName'], $bodyInfo['tableKey']);
            }

            $useInput = [
                'file_id' => $fileId,
                'file_type' => $bodyInfo['type'],
                'usage_type' => $bodyInfo['usageType'],
                'platform_id' => $bodyInfo['platformId'],
                'table_name' => $bodyInfo['tableName'],
                'table_column' => $bodyInfo['tableColumn'],
                'table_id' => $tableId,
                'table_key' => $bodyInfo['tableKey'] ?? null,
                'rating' => $bodyInfo['rating'] ?? 9,
                'account_id' => $accountId,
                'user_id' => $userId,
            ];

            FileUsage::create($useInput);
        }

        $fileTypeName = match ($bodyInfo['type']) {
            1 => 'images',
            2 => 'videos',
            3 => 'audios',
            4 => 'documents',
        };

        $fileInfo = FileHelper::fresnsAntiLinkFileInfoListByIds($fileIdArr)[$fileTypeName];

        return $fileInfo;
    }

    public static function saveFileInfoToDatabase(array $bodyInfo, string $diskPath, UploadedFile $file)
    {
        $imageWidth = null;
        $imageHeight = null;
        $imageIsLong = 0;
        if ($bodyInfo['type'] == 1) {
            $imageSize = getimagesize($file->path());
            $imageWidth = $imageSize[0] ?? null;
            $imageHeight = $imageSize[1] ?? null;

            if (! empty($imageWidth) >= 700) {
                if ($imageHeight >= $imageWidth * 3) {
                    $imageIsLong = 1;
                }
            }
        }

        $fileInput = [
            'fid' => Str::random(12),
            'type' => $bodyInfo['type'],
            'name' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
            'size' => $file->getSize(),
            'md5' => $bodyInfo['md5'] ?? null,
            'sha' => $bodyInfo['sha'] ?? null,
            'sha_type' =>  $bodyInfo['shaType'] ?? null,
            'path' => $diskPath,
            'image_width' => $imageWidth,
            'image_height' => $imageHeight,
            'image_is_long' => $imageIsLong,
            'more_json' => $bodyInfo['moreJson'] ?? null,
        ];

        $fileId = File::create($fileInput)->id;

        $accountId = PrimaryHelper::fresnsAccountIdByAid($bodyInfo['aid']);
        $userId = PrimaryHelper::fresnsUserIdByUidOrUsername($bodyInfo['uid']);

        $tableId = $bodyInfo['tableId'];
        if (empty($bodyInfo['tableId'])) {
            $tableId = PrimaryHelper::fresnsPrimaryId($bodyInfo['tableName'], $bodyInfo['tableKey']);
        }

        $useInput = [
            'file_id' => $fileId,
            'file_type' => $bodyInfo['type'],
            'usage_type' => $bodyInfo['usageType'],
            'platform_id' => $bodyInfo['platformId'],
            'table_name' => $bodyInfo['tableName'],
            'table_column' => $bodyInfo['tableColumn'],
            'table_id' => $tableId,
            'table_key' => $bodyInfo['tableKey'] ?? null,
            'account_id' => $accountId,
            'user_id' => $userId,
        ];
        FileUsage::create($useInput);

        return FileHelper::fresnsFileInfoById($fileId);
    }

    public static function logicalDeletionFiles(array $fileIdsOrFids)
    {
        File::whereIn('id', $fileIdsOrFids)->orWhereIn('fid', $fileIdsOrFids)->delete();

        return true;
    }
}
