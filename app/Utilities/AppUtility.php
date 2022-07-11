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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AppUtility
{
    public static function currentVersion()
    {
        return Cache::remember('currentVersion', 3600, function () {
            $fresnsJson = file_get_contents(
                base_path('fresns.json')
            );

            $currentVersion = json_decode($fresnsJson, true);

            return $currentVersion;
        });
    }

    public static function newVersion()
    {
        return Cache::remember('newVersion', 3600, function () {
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
        $currentVersion = AppUtility::currentVersion()['version'];
        $newVersion = AppUtility::newVersion()['version'];

        if (version_compare($currentVersion, $newVersion) == -1) {
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
            'networkTimezone' => '',
            'networkOffset' => '',
            'networkCurrency' => '',
            'networkIsp' => '',
            'networkOrg' => '',
            'networkAs' => '',
            'networkAsName' => '',
            'networkMobile' => '',
            'networkProxy' => '',
            'networkHosting' => '',
            'mapId' => '',
            'latitude' => '',
            'longitude' => '',
            'scale' => '',
            'continent' => '',
            'continentCode' => '',
            'country' => '',
            'countryCode' => '',
            'region' => '',
            'regionCode' => '',
            'city' => '',
            'district' => '',
            'zip' => '',
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

    public static function convertOptionToRequesParam(array $requestQuery)
    {
        $queryStatus = ConfigHelper::fresnsConfigByItemKey('menu_user_list_query_status');
        $queryConfig = ConfigHelper::fresnsConfigByItemKey('menu_user_list_query_config');

        // 转换为数组参数，封装
        $params = [];
        if (! empty($queryConfig)) {
            $urlInfo = parse_url($queryConfig);

            if (!empty($urlInfo['query'])) {
                parse_str($urlInfo['query'], $query);
            }
        }

        // 默认不允许用户传递参数
        $userQuery = [];

        // 禁止客户端传递参数改变数据进行请求
        if ($queryStatus == 1) {
            $userQuery = [];
        }

        // 只允许用户传递翻页参数
        if ($queryStatus == 2) {
            $userQuery = [
                'page' => $requestQuery['page'] ?? 1,
            ];
        }

        // 允许用户传递所有参数
        if ($queryStatus == 3) {
            $userQuery = $requestQuery;
        }

        // 参数覆盖
        return array_merge($params, $userQuery);
    }

    public static function convertApiDataToPaginate($items, $paginate)
    {
        if (method_exists($items, 'toArray')) {
            $items = $items->toArray();
        }

        $items = (array) $items;
        $total = $paginate['total'] ?? 0;
        $pageSize = $paginate['pageSize'] ?? 15;
        $paginate = new \Illuminate\Pagination\LengthAwarePaginator(
            items: $items,
            total: $total,
            perPage: $pageSize,
            currentPage: \request('page', 1),
        );

        $paginate->withPath('/'.\request()->path())->withQueryString();

        return $paginate;
    }
}
