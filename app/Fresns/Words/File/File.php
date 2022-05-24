<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Words\File;

use App\Fresns\Words\Config\WordConfig;
use App\Fresns\Words\File\DTO\GetAntiLinkFileInfoDTO;
use App\Fresns\Words\File\DTO\GetAntiLinkFileInfoListDTO;
use App\Fresns\Words\File\DTO\GetUploadTokenDTO;
use App\Fresns\Words\File\DTO\LogicalDeletionFileDTO;
use App\Fresns\Words\File\DTO\PhysicalDeletionFileDTO;
use App\Fresns\Words\File\DTO\UploadFileDTO;
use App\Fresns\Words\File\DTO\UploadFileInfoDTO;
use App\Helpers\ConfigHelper;
use App\Helpers\FileHelper;
use App\Helpers\PrimaryHelper;
use App\Models\File as FileModel;
use App\Models\FileAppend;
use App\Utilities\ConfigUtility;
use Fresns\CmdWordManager\Traits\CmdWordResponseTrait;

class File
{
    use CmdWordResponseTrait;

    /**
     * @param $wordBody
     * @return array
     *
     * @throws \Throwable
     */
    public function getUploadToken($wordBody)
    {
        $dtoWordBody = new GetUploadTokenDTO($wordBody);
        $langTag = \request()->header('langTag', config('app.locale'));

        $pluginUniKey = match ($dtoWordBody->type) {
            1 => ConfigHelper::fresnsConfigByItemKey('image_service'),
            2 => ConfigHelper::fresnsConfigByItemKey('video_service'),
            3 => ConfigHelper::fresnsConfigByItemKey('audio_service'),
            default => ConfigHelper::fresnsConfigByItemKey('document_service'),
        };

        if (empty($pluginUniKey)) {
            return $this->failure(
                21000,
                ConfigUtility::getCodeMessage(21000, 'CmdWord', $langTag),
            );
        }

        return \FresnsCmdWord::plugin($pluginUniKey)->getUploadToken($wordBody);
    }

    /**
     * @param $wordBody
     * @return array
     *
     * @throws \Throwable
     */
    public function uploadFile($wordBody)
    {
        $dtoWordBody = new UploadFileDTO($wordBody);
        $langTag = \request()->header('langTag', config('app.locale'));

        $fileConfigInfo = FileHelper::fresnsFileStorageConfigByType($dtoWordBody->type);

        if (empty($fileConfigInfo['service'])) {
            return $this->failure(
                21000,
                ConfigUtility::getCodeMessage(21000, 'CmdWord', $langTag),
            );
        }

        $accountId = PrimaryHelper::fresnsAccountIdByAid($dtoWordBody->aid ?? '');
        $userId = PrimaryHelper::fresnsUserIdByUid($dtoWordBody->uid ?? '');

        if (isset($dtoWordBody->tableKey)) {
            $tableId = $this->getTableId($dtoWordBody->tableName, $dtoWordBody->tableKey);
        }
        $uploadFile = $dtoWordBody->file;

        $storePath = $this->getFileTempPath($dtoWordBody->type.$dtoWordBody->tableType);
        $path = $uploadFile->store($storePath);
        $basePath = base_path();
        $basePath = $basePath.'/storage/app/';
        $newPath = $storePath.'/'.\Str::random(8).'.'.$uploadFile->getClientOriginalExtension();
        copy($basePath.$path, $basePath.$newPath);
        unlink($basePath.$path);

        $fileArr['fid'] = \Str::random(12);
        $fileArr['type'] = $dtoWordBody->type;
        $fileArr['name'] = $uploadFile->getClientOriginalName();
        $fileArr['mime'] = $uploadFile->getMimeType();
        $fileArr['extension'] = $uploadFile->getClientOriginalExtension();
        $fileArr['size'] = $uploadFile->getSize();
        $fileArr['md5'] = null;
        $fileArr['sha1'] = null;
        $fileArr['path'] = str_replace('public/', '', $newPath);
        $fileArr['more_json'] = $dtoWordBody->moreJson;
        if ($dtoWordBody->type == 1) {
            $imageSize = getimagesize($uploadFile);
            $fileArr['image_width'] = $imageSize[0] ?? null;
            $fileArr['image_height'] = $imageSize[1] ?? null;
            $fileArr['image_is_long'] = 0;
            if (! empty($fileArr['image_width']) >= 700) {
                if ($fileArr['image_height'] >= $fileArr['image_width'] * 3) {
                    $fileArr['image_is_long'] = 1;
                }
            }
        }
        $fid = $fileArr['fid'];
        $retId = FileModel::create($fileArr)->id;

        $appendInput = [
            'file_id' => $retId,
            'file_type' => $dtoWordBody->type,
            'platform_id' => $dtoWordBody->platformId,
            'table_type' => $dtoWordBody->tableType,
            'table_name' => $dtoWordBody->tableName,
            'table_column' => $dtoWordBody->tableColumn,
            'table_id' => isset($tableId) ?? null,
            'table_key' => $dtoWordBody->tableKey ?? null,
            'account_id' => isset($accountId) ?? null,
            'user_id' => isset($userId) ?? null,
        ];
        FileAppend::insert($appendInput);

        $fresnsResp = \FresnsCmdWord::plugin($unikey)->uploadFile([
            'fid' => $fid,
        ]);

        return $fresnsResp->getOrigin();
    }

    /**
     * @param $options
     * @return string
     */
    public function getFileTempPath($options)
    {
        $basePath = base_path().'/storage/app/public/';
        $fileTempPath = WordConfig::FILE_TEMP_PATH[$options] ?? '';
        if (empty($fileTempPath)) {
            $fileTempPath = '/temp_files/unknown/{ym}/{day}';
        }
        $fileTempPath = str_replace(['{ym}', '{day}'], [date('Ym', time()), date('d', time())], $fileTempPath);
        $realPath = $basePath.$fileTempPath;
        if (! is_dir($realPath)) {
            \Illuminate\Support\Facades\File::makeDirectory($realPath, 0755, true, true);
        }

        return 'public/'.$fileTempPath;
    }

    /**
     * @param $wordBody
     * @return array
     *
     * @throws \Throwable
     */
    public function uploadFileInfo($wordBody)
    {
        $dtoWordBody = new UploadFileInfoDTO($wordBody);
        $langTag = \request()->header('langTag', config('app.locale'));

        $fileConfigInfo = FileHelper::fresnsFileStorageConfigByType($dtoWordBody->type);

        if (empty($fileConfigInfo['service'])) {
            return $this->failure(
                21000,
                ConfigUtility::getCodeMessage(21000, 'CmdWord', $langTag),
            );
        }

        $accountId = PrimaryHelper::fresnsAccountIdByAid($dtoWordBody->aid ?? '');
        $userId = PrimaryHelper::fresnsUserIdByUid($dtoWordBody->uid ?? '');

        if (isset($dtoWordBody->tableKey)) {
            $tableId = $this->getTableId($dtoWordBody->tableName, $dtoWordBody->tableKey);
        }

        $fileInfoArr = json_decode($dtoWordBody->fileInfo, true);

        $fileIdArr = [];
        $fidArr = [];

        if ($fileInfoArr) {
            foreach ($fileInfoArr as $fileInfo) {
                $item = [];
                $item['fid'] = \Str::random(12);
                $item['type'] = $dtoWordBody->type;
                $item['name'] = $fileInfo['name'];
                $item['mime'] = $fileInfo['mime'] == '' ? null : $fileInfo['mime'];
                $item['extension'] = $fileInfo['extension'] == '' ? null : $fileInfo['extension'];
                $item['size'] = $fileInfo['size'] == '' ? null : $fileInfo['size'];
                $item['md5'] = $fileInfo['md5'] == '' ? null : $fileInfo['md5'];
                $item['sha1'] = $fileInfo['sha1'] == '' ? null : $fileInfo['sha1'];
                $item['path'] = $fileInfo['path'];
                $item['image_width'] = $fileInfo['imageWidth'] == '' ? null : $fileInfo['imageWidth'];
                $item['image_height'] = $fileInfo['imageHeight'] == '' ? null : $fileInfo['imageHeight'];
                $imageLong = 0;
                if (! empty($fileInfo['image_width'])) {
                    if ($fileInfo['image_width'] >= 700) {
                        if ($fileInfo['image_height'] >= $fileInfo['image_width'] * 3) {
                            $imageLong = 1;
                        } else {
                            $imageLong = 0;
                        }
                    }
                }
                $item['image_is_long'] = $imageLong;
                $item['video_time'] = $fileInfo['videoTime'] == '' ? null : $fileInfo['videoTime'];
                $item['video_cover'] = $fileInfo['videoCover'] == '' ? null : $fileInfo['videoCover'];
                $item['video_gif'] = $fileInfo['videoGif'] == '' ? null : $fileInfo['videoGif'];
                $item['audio_time'] = $fileInfo['audioTime'] == '' ? null : $fileInfo['audioTime'];
                $item['more_json'] = json_encode($fileInfo['moreJson']);

                $fieldId = FileModel::create($item)->id;
                $fileIdArr[] = $fieldId;
                $fidArr[] = $item['fid'];

                $append = [];
                $append['file_id'] = $fieldId;
                $append['file_type'] = $dtoWordBody->type;
                $append['platform_id'] = $dtoWordBody->platformId;
                $append['table_type'] = $dtoWordBody->tableType;
                $append['table_name'] = $dtoWordBody->tableName;
                $append['table_column'] = $dtoWordBody->tableColumn;
                $append['table_id'] = $tableId ?? null;
                $append['table_key'] = $dtoWordBody->tableKey ?? null;
                $append['rating'] = $fileInfo['rating'] ?? null;
                $append['account_id'] = $accountId;
                $append['user_id'] = $userId;
                $append['original_path'] = $fileInfo['originalPath'] == '' ? null : $fileInfo['originalPath'];

                FileAppend::insert($append);
            }
        }

        $fresnsResp = \FresnsCmdWord::plugin($unikey)->uploadFileInfo([
            'fids' => $fidArr,
        ]);

        return $fresnsResp->getOrigin();
    }

    /**
     * @param $wordBody
     * @return array
     *
     * @throws \Throwable
     */
    public function getAntiLinkFileInfoForImage($wordBody)
    {
        $dtoWordBody = new GetAntiLinkFileInfoDTO($wordBody);
        $langTag = \request()->header('langTag', config('app.locale'));

        $fileConfigInfo = FileHelper::fresnsFileStorageConfigByType(1);
        if (empty($fileConfigInfo['service'])) {
            return $this->failure(
                21000,
                ConfigUtility::getCodeMessage(21000, 'CmdWord', $langTag),
            );
        }

        $antiLinkStatus = FileHelper::fresnsFileAntiLinkStatusByType(1);

        if ($antiLinkStatus) {
            return \FresnsCmdWord::plugin($fileConfigInfo['service'])->getAntiLinkFileInfoForImage($dtoWordBody);
        } else {
            if (isset($dtoWordBody->fileId)) {
                return FileHelper::fresnsFileInfoByFid($dtoWordBody->fid);
            } else {
                return FileHelper::fresnsFileInfoById($dtoWordBody->fileId);
            }
        }
    }

    public function getAntiLinkFileInfoForVideo($wordBody)
    {
        $dtoWordBody = new GetAntiLinkFileInfoDTO($wordBody);
        $langTag = \request()->header('langTag', config('app.locale'));

        $fileConfigInfo = FileHelper::fresnsFileStorageConfigByType(2);
        if (empty($fileConfigInfo['service'])) {
            return $this->failure(
                21000,
                ConfigUtility::getCodeMessage(21000, 'CmdWord', $langTag),
            );
        }

        $antiLinkStatus = FileHelper::fresnsFileAntiLinkStatusByType(2);

        if ($antiLinkStatus) {
            return \FresnsCmdWord::plugin($fileConfigInfo['service'])->getAntiLinkFileInfoForVideo($dtoWordBody);
        } else {
            if (isset($dtoWordBody->fileId)) {
                return FileHelper::fresnsFileInfoByFid($dtoWordBody->fid);
            } else {
                return FileHelper::fresnsFileInfoById($dtoWordBody->fileId);
            }
        }
    }

    public function getAntiLinkFileInfoForAudio($wordBody)
    {
        $dtoWordBody = new GetAntiLinkFileInfoDTO($wordBody);
        $langTag = \request()->header('langTag', config('app.locale'));

        $fileConfigInfo = FileHelper::fresnsFileStorageConfigByType(3);
        if (empty($fileConfigInfo['service'])) {
            return $this->failure(
                21000,
                ConfigUtility::getCodeMessage(21000, 'CmdWord', $langTag),
            );
        }

        $antiLinkStatus = FileHelper::fresnsFileAntiLinkStatusByType(3);

        if ($antiLinkStatus) {
            return \FresnsCmdWord::plugin($fileConfigInfo['service'])->getAntiLinkFileInfoForAudio($dtoWordBody);
        } else {
            if (isset($dtoWordBody->fileId)) {
                return FileHelper::fresnsFileInfoByFid($dtoWordBody->fid);
            } else {
                return FileHelper::fresnsFileInfoById($dtoWordBody->fileId);
            }
        }
    }

    public function getAntiLinkFileInfoForDocument($wordBody)
    {
        $dtoWordBody = new GetAntiLinkFileInfoDTO($wordBody);
        $langTag = \request()->header('langTag', config('app.locale'));

        $fileConfigInfo = FileHelper::fresnsFileStorageConfigByType(4);
        if (empty($fileConfigInfo['service'])) {
            return $this->failure(
                21000,
                ConfigUtility::getCodeMessage(21000, 'CmdWord', $langTag),
            );
        }

        $antiLinkStatus = FileHelper::fresnsFileAntiLinkStatusByType(4);

        if ($antiLinkStatus) {
            return \FresnsCmdWord::plugin($fileConfigInfo['service'])->getAntiLinkFileInfoForDocument($dtoWordBody);
        } else {
            if (isset($dtoWordBody->fileId)) {
                return FileHelper::fresnsFileInfoByFid($dtoWordBody->fid);
            } else {
                return FileHelper::fresnsFileInfoById($dtoWordBody->fileId);
            }
        }
    }

    /**
     * @param $wordBody
     * @return array
     *
     * @throws \Throwable
     */
    public function getAntiLinkFileInfoForImageList($wordBody)
    {
        $dtoWordBody = new GetAntiLinkFileInfoListDTO($wordBody);
        $langTag = \request()->header('langTag', config('app.locale'));

        $fileConfigInfo = FileHelper::fresnsFileStorageConfigByType(1);
        if (empty($fileConfigInfo['service'])) {
            return $this->failure(
                21000,
                ConfigUtility::getCodeMessage(21000, 'CmdWord', $langTag),
            );
        }

        $antiLinkStatus = FileHelper::fresnsFileAntiLinkStatusByType(1);

        if ($antiLinkStatus) {
            return \FresnsCmdWord::plugin($fileConfigInfo['service'])->getAntiLinkFileInfoForImageList($dtoWordBody);
        }

        return FileHelper::fresnsFileInfoList($dtoWordBody->tableName, $dtoWordBody->tableColumn, $dtoWordBody->tableId, $dtoWordBody->tableKey);
    }

    public function getAntiLinkFileInfoForVideoList($wordBody)
    {
        $dtoWordBody = new GetAntiLinkFileInfoListDTO($wordBody);
        $langTag = \request()->header('langTag', config('app.locale'));

        $fileConfigInfo = FileHelper::fresnsFileStorageConfigByType(2);
        if (empty($fileConfigInfo['service'])) {
            return $this->failure(
                21000,
                ConfigUtility::getCodeMessage(21000, 'CmdWord', $langTag),
            );
        }

        $antiLinkStatus = FileHelper::fresnsFileAntiLinkStatusByType(2);

        if ($antiLinkStatus) {
            return \FresnsCmdWord::plugin($fileConfigInfo['service'])->getAntiLinkFileInfoForVideoList($dtoWordBody);
        }

        return FileHelper::fresnsFileInfoList($dtoWordBody->tableName, $dtoWordBody->tableColumn, $dtoWordBody->tableId, $dtoWordBody->tableKey);
    }

    public function getAntiLinkFileInfoForAudioList($wordBody)
    {
        $dtoWordBody = new GetAntiLinkFileInfoListDTO($wordBody);
        $langTag = \request()->header('langTag', config('app.locale'));

        $fileConfigInfo = FileHelper::fresnsFileStorageConfigByType(3);
        if (empty($fileConfigInfo['service'])) {
            return $this->failure(
                21000,
                ConfigUtility::getCodeMessage(21000, 'CmdWord', $langTag),
            );
        }

        $antiLinkStatus = FileHelper::fresnsFileAntiLinkStatusByType(3);

        if ($antiLinkStatus) {
            return \FresnsCmdWord::plugin($fileConfigInfo['service'])->getAntiLinkFileInfoForAudioList($dtoWordBody);
        }

        return FileHelper::fresnsFileInfoList($dtoWordBody->tableName, $dtoWordBody->tableColumn, $dtoWordBody->tableId, $dtoWordBody->tableKey);
    }

    public function getAntiLinkFileInfoForDocumentList($wordBody)
    {
        $dtoWordBody = new GetAntiLinkFileInfoListDTO($wordBody);
        $langTag = \request()->header('langTag', config('app.locale'));

        $fileConfigInfo = FileHelper::fresnsFileStorageConfigByType(4);
        if (empty($fileConfigInfo['service'])) {
            return $this->failure(
                21000,
                ConfigUtility::getCodeMessage(21000, 'CmdWord', $langTag),
            );
        }

        $antiLinkStatus = FileHelper::fresnsFileAntiLinkStatusByType(4);

        if ($antiLinkStatus) {
            return \FresnsCmdWord::plugin($fileConfigInfo['service'])->getAntiLinkFileInfoForDocumentList($dtoWordBody);
        }

        return FileHelper::fresnsFileInfoList($dtoWordBody->tableName, $dtoWordBody->tableColumn, $dtoWordBody->tableId, $dtoWordBody->tableKey);
    }

    /**
     * @param $wordBody
     * @return array
     *
     * @throws \Throwable
     */
    public function logicalDeletionFile($wordBody)
    {
        $dtoWordBody = new LogicalDeletionFileDTO($wordBody);
        $langTag = \request()->header('langTag', config('app.locale'));

        if (isset($dtoWordBody->fileId)) {
            $file = FileModel::whereFid($dtoWordBody->fid)->firstOrFail();
        } else {
            $file = FileModel::whereId($dtoWordBody->fileId)->firstOrFail();
        }

        if (empty($file)) {
            return $this->failure(
                21009,
                ConfigUtility::getCodeMessage(21009, 'CmdWord', $langTag),
            );
        }

        FileModel::where($file->id)->delete();

        return $this->success();
    }

    /**
     * @param $wordBody
     * @return array
     *
     * @throws \Throwable
     */
    public function physicalDeletionFile($wordBody)
    {
        $dtoWordBody = new PhysicalDeletionFileDTO($wordBody);
        $langTag = \request()->header('langTag', config('app.locale'));

        if (isset($dtoWordBody->fileId)) {
            $file = FileModel::whereFid($dtoWordBody->fid)->firstOrFail();
        } else {
            $file = FileModel::whereId($dtoWordBody->fileId)->firstOrFail();
        }

        if (empty($file)) {
            return $this->failure(
                21008,
                ConfigUtility::getCodeMessage(21008, 'CmdWord', $langTag),
            );
        }

        $fileConfigInfo = FileHelper::fresnsFileStorageConfigByType($file->type);
        if (empty($fileConfigInfo['service'])) {
            return $this->failure(
                21000,
                ConfigUtility::getCodeMessage(21000, 'CmdWord', $langTag),
            );
        }

        return \FresnsCmdWord::plugin($fileConfigInfo['service'])->physicalDeletionFile($wordBody);
    }

    /**
     * @param $tableName
     * @param $tableId
     * @return mixed
     */
    protected function getTableId($tableName, $tableKey)
    {
        $tableId = match ($tableName) {
            'accounts'=>PrimaryHelper::fresnsAccountIdByAid($tableKey),
            'users'=>PrimaryHelper::fresnsUserIdByUid($tableKey),
            'posts'=>PrimaryHelper::fresnsPostIdByPid($tableKey),
            'comments'=>PrimaryHelper::fresnsCommentIdByCid($tableKey),
            'extends'=>PrimaryHelper::fresnsExtendIdByEid($tableKey),
            'groups'=>PrimaryHelper::fresnsGroupIdByGid($tableKey),
            'hashtags'=>PrimaryHelper::fresnsHashtagIdByHid($tableKey),
            default => null,
        };

        return $tableId;
    }
}
