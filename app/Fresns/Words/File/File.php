<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Words\File;

use App\Fresns\Words\File\DTO\GetAntiLinkFileInfoDTO;
use App\Fresns\Words\File\DTO\GetAntiLinkFileInfoListDTO;
use App\Fresns\Words\File\DTO\GetAntiLinkFileOriginalUrlDTO;
use App\Fresns\Words\File\DTO\GetUploadTokenDTO;
use App\Fresns\Words\File\DTO\LogicalDeletionFileDTO;
use App\Fresns\Words\File\DTO\PhysicalDeletionFileDTO;
use App\Fresns\Words\File\DTO\UploadFileDTO;
use App\Fresns\Words\File\DTO\UploadFileInfoDTO;
use App\Helpers\FileHelper;
use App\Utilities\ConfigUtility;
use App\Utilities\FileUtility;
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

        $storageConfig = FileHelper::fresnsFileStorageConfigByType($dtoWordBody->type);

        if (! $storageConfig['storageConfigStatus']) {
            return $this->failure(
                21000,
                ConfigUtility::getCodeMessage(21000, 'CmdWord', $langTag),
            );
        }

        return \FresnsCmdWord::plugin($storageConfig['service'])->getUploadToken($dtoWordBody);
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

        // $bodyInfo = [
        //     'platformId' => $dtoWordBody->platformId,
        //     'useType' => $dtoWordBody->useType,
        //     'tableName' => $dtoWordBody->tableName,
        //     'tableColumn' => $dtoWordBody->tableColumn,
        //     'tableId' => $dtoWordBody->tableId,
        //     'tableKey' => $dtoWordBody->tableKey,
        //     'aid' => $dtoWordBody->aid,
        //     'uid' => $dtoWordBody->uid,
        //     'type' => $dtoWordBody->type,
        //     'moreJson' => $dtoWordBody->moreJson,
        // ];

        // $uploadFile = FileUtility::uploadFile($bodyInfo, $dtoWordBody->file);

        $storageConfig = FileHelper::fresnsFileStorageConfigByType($dtoWordBody->type);

        if (! $storageConfig['storageConfigStatus']) {
            return $this->failure(
                21000,
                ConfigUtility::getCodeMessage(21000, 'CmdWord', $langTag),
            );
        }

        return \FresnsCmdWord::plugin($storageConfig['service'])->uploadFile($dtoWordBody);
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

        $storageConfig = FileHelper::fresnsFileStorageConfigByType($dtoWordBody->type);

        if (! $storageConfig['storageConfigStatus']) {
            return $this->failure(
                21000,
                ConfigUtility::getCodeMessage(21000, 'CmdWord', $langTag),
            );
        }

        return \FresnsCmdWord::plugin($storageConfig['service'])->uploadFileInfo($dtoWordBody);
    }

    /**
     * @param $wordBody
     * @return array
     *
     * @throws \Throwable
     */
    public function getAntiLinkFileInfo($wordBody)
    {
        $dtoWordBody = new GetAntiLinkFileInfoDTO($wordBody);
        $langTag = \request()->header('langTag', config('app.locale'));

        $storageConfig = FileHelper::fresnsFileStorageConfigByType($dtoWordBody->type);

        if (! $storageConfig['storageConfigStatus']) {
            return $this->failure(
                21000,
                ConfigUtility::getCodeMessage(21000, 'CmdWord', $langTag),
            );
        }

        if ($storageConfig['antiLinkConfigStatus']) {
            return \FresnsCmdWord::plugin($storageConfig['service'])->getAntiLinkFileInfo($dtoWordBody);
        }

        return $this->success(FileHelper::fresnsFileInfo($dtoWordBody->fileId ?? $dtoWordBody->fid));
    }

    /**
     * @param $wordBody
     * @return array
     *
     * @throws \Throwable
     */
    public function getAntiLinkFileInfoList($wordBody)
    {
        $dtoWordBody = new GetAntiLinkFileInfoListDTO($wordBody);
        $langTag = \request()->header('langTag', config('app.locale'));

        $storageConfig = FileHelper::fresnsFileStorageConfigByType($dtoWordBody->type);

        if (! $storageConfig['storageConfigStatus']) {
            return $this->failure(
                21000,
                ConfigUtility::getCodeMessage(21000, 'CmdWord', $langTag),
            );
        }

        if ($storageConfig['antiLinkConfigStatus']) {
            return \FresnsCmdWord::plugin($storageConfig['service'])->getAntiLinkFileInfoList($dtoWordBody);
        }

        return $this->success(FileHelper::fresnsFileInfoList($dtoWordBody->ids, $dtoWordBody->idType));
    }

    /**
     * @param $wordBody
     * @return array
     *
     * @throws \Throwable
     */

    public function getAntiLinkFileOriginalUrl($wordBody)
    {
        $dtoWordBody = new GetAntiLinkFileOriginalUrlDTO($wordBody);
        $langTag = \request()->header('langTag', config('app.locale'));

        $storageConfig = FileHelper::fresnsFileStorageConfigByType($dtoWordBody->type);

        if (! $storageConfig['storageConfigStatus']) {
            return $this->failure(
                21000,
                ConfigUtility::getCodeMessage(21000, 'CmdWord', $langTag),
            );
        }

        if ($storageConfig['antiLinkConfigStatus']) {
            return \FresnsCmdWord::plugin($storageConfig['service'])->getAntiLinkFileOriginalUrl($dtoWordBody);
        }

        return $this->success([
            'originalUrl' => FileHelper::fresnsFileOriginalUrl($dtoWordBody->fileId ?? $dtoWordBody->fid),
        ]);
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

        FileUtility::logicalDeletionFile($dtoWordBody->ids, $dtoWordBody->idType);

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

        $storageConfig = FileHelper::fresnsFileStorageConfigByType($dtoWordBody->type);

        if (! $storageConfig['storageConfigStatus']) {
            return $this->failure(
                21000,
                ConfigUtility::getCodeMessage(21000, 'CmdWord', $langTag),
            );
        }

        return \FresnsCmdWord::plugin($storageConfig['service'])->physicalDeletionFile($dtoWordBody);
    }
}
