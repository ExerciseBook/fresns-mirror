<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models\Traits;

use App\Helpers\ConfigHelper;
use App\Helpers\FileHelper;
use App\Helpers\StrHelper;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

trait FileServiceTrait
{
    public function getFileOriginalUrl()
    {
        $fileData = $this;

        $fileConfigInfo = FileHelper::fresnsFileStorageConfigByType($fileData->type);

        $path = $fileData->fileAppend->original_path ?? $fileData->path;

        $originalUrl = StrHelper::qualifyUrl($path, $fileConfigInfo['bucketDomain']);

        return $originalUrl;
    }

    public function getFileInfo()
    {
        $fileData = $this;

        if ($fileData->size > 1048576) {
            $fileSize = round($fileData->size / 1048576);
            $fileSizeInfo = $fileSize.' MB';
        } elseif ($fileData->size > 1024) {
            $fileSize = round($fileData->size / 1024, 2);
            $fileSizeInfo = $fileSize.' KB';
        } else {
            $fileSizeInfo = $fileData->size.' B';
        }
        $info['fid'] = $fileData->fid;
        $info['type'] = $fileData->type;
        $info['name'] = $fileData->name;
        $info['mime'] = $fileData->mime;
        $info['extension'] = $fileData->extension;
        $info['size'] = $fileSizeInfo;
        $info['md5'] = $fileData->md5;
        $info['sha1'] = $fileData->sha1;
        $info['more_json'] = $fileData->more_json;

        $fileMetaInfo = $this->getFileMetaInfoByType();

        return array_merge($info, $fileMetaInfo);
    }

    public function getFileMetaInfoByType()
    {
        $info = match ($this->type) {
            File::TYPE_IMAGE => $this->getImageMetaInfo(),
            File::TYPE_VIDEO => $this->getVideoMetaInfo(),
            File::TYPE_AUDIO => $this->getAudioMetaInfo(),
            File::TYPE_DOCUMENT => $this->getDocumentMetaInfo(),
            default => throw new \LogicException('unknown file type '.$this->type),
        };

        return $info;
    }

    public function getImageMetaInfo()
    {
        $fileData = $this;

        $imageConfig = ConfigHelper::fresnsConfigByItemKeys([
            'image_bucket_domain',
            'image_thumb_config',
            'image_thumb_avatar',
            'image_thumb_ratio',
            'image_thumb_square',
            'image_thumb_big'
        ]);

        $filePath = Storage::url($fileData->path);

        $imageDefaultUrl = StrHelper::qualifyUrl($filePath, $imageConfig['image_bucket_domain']);

        $info['imageWidth'] = $fileData->image_width;
        $info['imageHeight'] = $fileData->image_height;
        $info['imageLong'] = (bool) $fileData->image_is_long;
        $info['imageDefaultUrl'] = $imageDefaultUrl;
        $info['imageConfigUrl'] = $imageDefaultUrl.$imageConfig['image_thumb_config'];
        $info['imageAvatarUrl'] = $imageDefaultUrl.$imageConfig['image_thumb_avatar'];
        $info['imageRatioUrl'] = $imageDefaultUrl.$imageConfig['image_thumb_ratio'];
        $info['imageSquareUrl'] = $imageDefaultUrl.$imageConfig['image_thumb_square'];
        $info['imageBigUrl'] = $imageDefaultUrl.$imageConfig['image_thumb_big'];

        return $info;
    }

    public function getVideoMetaInfo()
    {
        $fileData = $this;

        $videoBucketDomain = ConfigHelper::fresnsConfigByItemKey('video_bucket_domain');

        $videoCoverPath = Storage::url($fileData->video_cover);
        $videoGifPath = Storage::url($fileData->video_gif);
        $filePath = Storage::url($fileData->path);

        $info['videoTime'] = $fileData->video_time;
        $info['videoCoverUrl'] = StrHelper::qualifyUrl($videoCoverPath, $videoBucketDomain);
        $info['videoGifUrl'] = StrHelper::qualifyUrl($videoGifPath, $videoBucketDomain);
        $info['videoUrl'] = StrHelper::qualifyUrl($filePath, $videoBucketDomain);
        $info['transcodingState'] = $fileData->transcoding_state;

        return $info;
    }

    public function getAudioMetaInfo()
    {
        $fileData = $this;

        $audioBucketDomain = ConfigHelper::fresnsConfigByItemKey('audio_bucket_domain');

        $filePath = Storage::url($fileData->path);

        $info['audioTime'] = $fileData->audio_time;
        $info['audioUrl'] = StrHelper::qualifyUrl($filePath, $audioBucketDomain);
        $info['transcodingState'] = $fileData->transcoding_state;

        return $info;
    }

    public function getDocumentMetaInfo()
    {
        $fileData = $this;

        $documentBucketDomain = ConfigHelper::fresnsConfigByItemKey('document_bucket_domain');

        $filePath = Storage::url($fileData->path);

        $info['documentUrl'] = StrHelper::qualifyUrl($filePath, $documentBucketDomain);

        return $info;
    }
}
