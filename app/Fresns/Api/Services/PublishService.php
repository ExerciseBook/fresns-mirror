<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Services;

use App\Exceptions\ApiException;
use App\Fresns\Api\Traits\ApiHeaderTrait;
use App\Fresns\Api\Traits\ApiResponseTrait;
use App\Helpers\DateHelper;
use App\Utilities\ConfigUtility;
use App\Utilities\PermissionUtility;
use Carbon\Carbon;

class PublishService
{
    use ApiHeaderTrait;
    use ApiResponseTrait;

    // check publish perm
    // $type = post / comment
    public function checkPublishPerm(string $type, ?int $mainId = null)
    {
        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUser = $this->user();

        // Check time limit
        $contentInterval = PermissionUtility::checkContentIntervalTime($authUser->id, $type);
        if (! $contentInterval && ! $mainId) {
            throw new ApiException(36117);
        }

        $publishConfig = ConfigUtility::getPublishConfigByType($authUser->id, $type, $langTag, $timezone);

        // Check publication requirements
        if (! $publishConfig['perm']['publish']) {
            return $this->failure(
                36104,
                ConfigUtility::getCodeMessage(36104, 'Fresns', $langTag),
                $publishConfig['perm']['tips'],
            );
        }

        // Check additional requirements
        if ($publishConfig['limit']['status']) {
            switch ($publishConfig['limit']['type']) {
                // period Y-m-d H:i:s
                case 1:
                    $dbDateTime = DateHelper::fresnsDatabaseCurrentDateTime();
                    $newDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $dbDateTime);
                    $periodStart = Carbon::createFromFormat('Y-m-d H:i:s', $publishConfig['limit']['periodStart']);
                    $periodEnd = Carbon::createFromFormat('Y-m-d H:i:s', $publishConfig['limit']['periodEnd']);

                    $isInTime = $newDateTime->between($periodStart, $periodEnd);
                    if ($isInTime) {
                        throw new ApiException(36304);
                    }
                break;

                // cycle H:i
                case 2:
                    $dbDateTime = DateHelper::fresnsDatabaseCurrentDateTime();
                    $newDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $dbDateTime);
                    $dbDate = date('Y-m-d', $dbDateTime);
                    $cycleStart = "{$dbDate} {$publishConfig['limit']['cycleStart']}:00"; // Y-m-d H:i:s
                    $cycleEnd = "{$dbDate} {$publishConfig['limit']['cycleEnd']}:00"; // Y-m-d H:i:s

                    $periodStart = Carbon::createFromFormat('Y-m-d H:i:s', $cycleStart); // 2022-07-01 22:30:00
                    $periodEnd = Carbon::createFromFormat('Y-m-d H:i:s', $cycleEnd); // 2022-07-01 08:30:00

                    if ($periodEnd->lt($periodStart)) {
                        // next day 2022-07-02 08:30:00
                        $periodEnd = $periodEnd->addDay();
                    }

                    $isInTime = $newDateTime->between($periodStart, $periodEnd);
                    if ($isInTime) {
                        throw new ApiException(36304);
                    }
                break;
            }
        }
    }
}
