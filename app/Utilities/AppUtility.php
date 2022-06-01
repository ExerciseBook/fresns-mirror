<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Utilities;

use App\Helpers\AppHelper;
use App\Helpers\ConfigHelper;
use Browser;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class AppUtility
{
    public static function currentVersion()
    {
        return \Cache::remember('currentVersion', 3600, function () {
            $fresnsJson = file_get_contents(
                base_path('fresns.json')
            );

            $currentVersion = json_decode($fresnsJson, true);

            return $currentVersion;
        });
    }

    public static function newVersion()
    {
        return \Cache::remember('newVersion', 3600, function () {
            try {
                $versionInfoUrl = AppUtility::getApiHost().'/version.json';
                $client = new \GuzzleHttp\Client();
                $response = $client->request('GET', $versionInfoUrl);
                $versionInfo = json_decode($response->getBody(), true);
                $buildType = ConfigHelper::fresnsConfigByItemKey('build_type');

                if ($buildType == 1) {
                    return $versionInfo['stableBuild'];
                }

                return $versionInfo['betaBuild'];
            } catch (\Exception $e) {
                return AppHelper::getAppVersion();
            }
        });
    }

    public static function checkVersion(): bool
    {
        $currentVersionInt = AppUtility::currentVersion()['versionInt'];
        $newVersionInt = AppUtility::newVersion()['versionInt'];

        if ($currentVersionInt < $newVersionInt) {
            return true; // There is a new version
        }

        return false; // No new version
    }

    public static function editVersion(string $version, int $versionInt)
    {
        $fresnsJson = file_get_contents(
            $path = base_path('fresns.json')
        );

        $currentVersion = json_decode($fresnsJson, true);

        $currentVersion['version'] = $version;
        $currentVersion['versionInt'] = $versionInt;

        $editContent = json_encode($currentVersion, JSON_PRETTY_PRINT);
        file_put_contents($path, $editContent);

        return true;
    }

    public static function getApiHost()
    {
        $apiHost = base64_decode('aHR0cHM6Ly9hcGkuZnJlc25zLmNu', true);

        return $apiHost;
    }

    public static function macroMarketHeader()
    {
        Http::macro('market', function () {
            return Http::withHeaders(
                AppUtility::getMarketHeader()
            )
            ->baseUrl(
                AppUtility::getApiHost()
            );
        });
    }

    public static function getMarketHeader(): array
    {
        $isHttps = \request()->getScheme() === 'https';

        $appConfig = ConfigHelper::fresnsConfigByItemKeys([
            'install_datetime',
            'build_type',
            'site_name',
            'site_desc',
            'site_copyright',
            'default_timezone',
            'default_language',
        ]);

        $header = [
            'panelLangTag' => \App::getLocale(),
            'installDatetime' => $appConfig['install_datetime'],
            'buildType' => $appConfig['build_type'],
            'version' => self::currentVersion()['version'],
            'versionInt' => self::currentVersion()['versionInt'],
            'httpSsl' => $isHttps ? 1 : 0,
            'httpHost' => \request()->getHttpHost(),
            'siteName' => $appConfig['site_name'],
            'siteDesc' => $appConfig['site_desc'],
            'siteCopyright' => $appConfig['site_copyright'],
            'timezone' => $appConfig['default_timezone'],
            'language' => $appConfig['default_language'],
        ];

        return $header;
    }

    public static function getDeviceInfo(): array
    {
        $deviceInfo = [
            'type' => Browser::deviceType(),
            'brand' => Browser::deviceFamily(),
            'model' => Browser::deviceModel(),
            'platformName' => Browser::platformFamily(),
            'platformVersion' => Browser::platformVersion(),
            'browserName' => Browser::browserFamily(),
            'browserVersion' => Browser::browserVersion(),
            'browserEngine' => Browser::browserEngine(),
            'networkType' => '',
            'networkIpv4' => request()->ip(),
            'networkIpv6' => '',
            'networkPort' => $_SERVER['REMOTE_PORT'] ?? '',
            'mapId' => '',
            'latitude' => '',
            'longitude' => '',
            'scale' => '',
            'nation' => '',
            'province' => '',
            'city' => '',
            'district' => '',
            'adcode' => '',
            'positionName' => '',
            'address' => '',
        ];

        return $deviceInfo;
    }

    public static function executeUpgradeCommand(): bool
    {
        logger('upgrade:fresns upgrade command');

        $currentVersionInt = AppUtility::currentVersion()['versionInt'] ?? 0;
        $newVersionInt = AppUtility::newVersion()['versionInt'] ?? 0;

        if (! $currentVersionInt || ! $newVersionInt) {
            return false;
        }

        $versionInt = $currentVersionInt;

        while ($versionInt <= $newVersionInt) {
            $versionInt++;
            $command = 'fresns:upgrade-'.$versionInt;
            if (\Artisan::has($command)) {
                \Artisan::call($command);
            }
        }

        return true;
    }

    public static function ensureDistanceFunctionExists()
    {
        $getDistanceSqlFunctionExists = \Illuminate\Support\Facades\Cache::remember('get_distance_sql_exists', now()->addDays(7), function () {
            $getDistanceSqlFunctionSql = "SHOW FUNCTION STATUS where name = 'get_distance'";
            $getDistanceSqlFunction = \Illuminate\Support\Facades\DB::selectOne($getDistanceSqlFunctionSql);
            $getDistanceSqlFunctionExists = boolval($getDistanceSqlFunction);

            if ($getDistanceSqlFunctionExists) {
                return true;
            }

            $createGetDistanceFunctionSql = <<<SQL
drop function if exists get_distance;
delimiter //
create function get_distance (
  lng1 double,
  lat1 double,
  lng2 double,
  lat2 double
)
returns double
begin
    declare distance double;
    declare a double;
    declare b double;

    declare radLat1 double;
    declare radLat2 double;
    declare radLng1 double;
    declare radLng2 double;
 
    set radLat1 = lat1 * PI() / 180;
    set radLat2 = lat2 * PI() / 180;
    set radLng1 = lng1 * PI() / 180;
    set radLng2 = lng2 * PI() / 180;

    set a = radLat1 - radLat2;
    set b = radLng1 - radLng2;

    set distance = 2 * asin(
      sqrt(
        pow(sin(a / 2), 2) + cos(radLat1) * cos(radLat2) * pow(sin(b / 2), 2)
      )
    ) * 6378.137;
    return distance;
end
//
delimiter ;
SQL;

            return \Illuminate\Support\Facades\DB::statement($createGetDistanceFunctionSql);
        });

        if (!$getDistanceSqlFunctionExists) {
            \Illuminate\Support\Facades\Cache::forget('get_distance_sql_exists');
        }

        return $getDistanceSqlFunctionExists;
    }

    public static function getDistanceSql($sqlLongitude, $sqlLatitude, $longitude, $latitude, $alias = 'distance')
    {
        $sql = <<<SQL
2 * ASIN(
      SQRT(
        POW(
          SIN(
            (
                $latitude * PI() / 180 - $sqlLatitude * PI() / 180
            ) / 2
          ), 2
        ) + COS($latitude * PI() / 180) * COS($sqlLatitude * PI() / 180) * POW(
          SIN(
            (
                $longitude * PI() / 180 - $sqlLongitude * PI() / 180
            ) / 2
          ), 2
        )
      )
    ) * 6378.137
SQL;
        return sprintf('(%s) as %s', $sql, $alias);
    }

    public static function isForbidden(?User $user)
    {
        if (is_null($user)) {
            return false;
        }

        if (now()->gt($user?->expired_at)) {
            $sitePrivateEnd = ConfigHelper::fresnsConfigByItemKey('site_private_end');

            return $sitePrivateEnd == 1;
        }

        return false;
    }

    public static function isPrivate(?User $user)
    {
        if (is_null($user)) {
            return false;
        }

        if (now()->gt($user->expired_at)) {
            $sitePrivateEnd = ConfigHelper::fresnsConfigByItemKey('site_private_end');

            return $sitePrivateEnd == 2;
        }

        return false;
    }
}
