<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Console\Commands;

use App\Helpers\AppHelper;
use App\Models\Plugin;
use App\Utilities\AppUtility;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class PhysicalUpgradeFresns extends Command
{
    protected $signature = 'fresns:physical-upgrade';

    protected $description = 'physical upgrade fresns';

    public function __construct()
    {
        parent::__construct();
    }

    // execute the console command
    public function handle()
    {
        Cache::put('physicalUpgrading', 1);
        Cache::put('physicalUpgradeOutput', '');

        // Check if an upgrade is needed
        $checkVersion = AppUtility::checkVersion();
        if (! $checkVersion) {
            Cache::forget('physicalUpgrading');

            $this->info('No new version, Already the latest version of Fresns.');
            return -1;
        }

        try {
            $this->updateOutput('Step 1/5: update data'."\n");
            if (!AppUtility::executeUpgradeCommand()) {
                $this->updateOutput("\n".'没有新版本或不存在更新命令，更新失败'."\n");
                return -1;
            }

            $this->updateOutput("\n".'Step 2/5: install plugins composer'."\n");
            if (!$this->pluginComposerInstall()) {
                return -1;
            }

            $this->updateOutput("\n".'Step 3/5: publish and activate plugins or themes'."\n");
            if (!$this->pluginPublishAndActivate()) {
                return -1;
            }

            $this->updateOutput("\n".'Step 4/5: update version'."\n");
            $this->upgradeFinish();

            $this->updateOutput("\n".'Step 5/5: clear cache'."\n");
            $this->clear();
        } catch (\Exception $e) {
            logger($e->getMessage());
            $this->info($e->getMessage());
        }

        return Command::SUCCESS;
    }

    // output artisan info
    public function updateOutput($content = '')
    {
        $this->info($content);
        $output = cache('physicalUpgradeOutput');
        $output = $content;

        return Cache::put('physicalUpgradeOutput', $output);
    }

    // step 1: execute the version command
    // try AppUtility executeUpgradeCommand()

    // step 2: composer all plugins
    public function pluginComposerInstall()
    {
        try {
            $exitCode = \Artisan::call('plugin:composer-update');
            $this->updateOutput(\Artisan::output());
            if ($exitCode) {
                return false;
            }
        } catch (\Exception $e) {
            logger($e->getMessage());
            $this->info($e->getMessage());
            return false;
        }

        return true;
    }

    // step 3: publish and activate plugins or themes
    public function pluginPublishAndActivate()
    {
        $plugins = Plugin::all();

        foreach ($plugins as $plugin) {
            try {
                if ($plugin->type == 4) {
                    $exitCode = \Artisan::call('theme:publish', ['plugin' => $plugin->unikey]);
                    $this->updateOutput(\Artisan::output());
                    if ($exitCode) {
                        return false;
                    }

                    if ($plugin->is_enable) {
                        $exitCode = \Artisan::call('theme:activate', ['plugin' => $plugin->unikey]);
                        $this->updateOutput(\Artisan::output());
                        if ($exitCode) {
                            return false;
                        }
                    }
                } else {
                    $exitCode = \Artisan::call('plugin:publish', ['plugin' => $plugin->unikey]);
                    $this->updateOutput(\Artisan::output());
                    if ($exitCode) {
                        return false;
                    }

                    if ($plugin->is_enable) {
                        $exitCode = \Artisan::call('plugin:activate', ['plugin' => $plugin->unikey]);
                        $this->updateOutput(\Artisan::output());
                        if ($exitCode) {
                            return false;
                        }
                    }
                }
            } catch (\Exception $e) {
                logger($e->getMessage());
                $this->info($e->getMessage());
                return false;
            }
        }

        return true;
    }

    // step 4: edit fresns version info
    public function upgradeFinish(): bool
    {
        $newVersion = AppHelper::VERSION;
        $newVersionInt = AppHelper::VERSION_INT;

        AppUtility::editVersion($newVersion, $newVersionInt);

        return true;
    }

    // step 5: clear cache
    public function clear()
    {
        logger('upgrade:clear');

        \Artisan::call('config:clear');
        $this->updateOutput(\Artisan::output());

        $output = cache('physicalUpgradeOutput');
        \Artisan::call('cache:clear');

        $this->updateOutput($output.\Artisan::output());

        $this->updateOutput("\n".__('FsLang::tips.upgradeSuccess'));
    }
}
