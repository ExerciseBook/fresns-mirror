<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Console\Commands\Upgrade;

use App\Models\Config;
use App\Models\Language;
use App\Utilities\ArrUtility;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class Upgrade7Command extends Command
{
    protected $signature = 'fresns:upgrade-7';

    protected $description = 'upgrade to fresns v2.0.0-beta.6';

    public function __construct()
    {
        parent::__construct();
    }

    // execute the console command
    public function handle()
    {
        logger('upgrade:fresns-7 composerInstall');
        $this->composerInstall();

        logger('upgrade:fresns-7 updateData');
        $this->updateData();

        return Command::SUCCESS;
    }

    // composer install
    public function composerInstall()
    {
        $composerPath = 'composer';

        if (! $this->commandExists($composerPath)) {
            $composerPath = '/usr/bin/composer';
        }

        $process = new Process([$composerPath, 'install'], base_path());
        $process->setTimeout(0);
        $process->start();

        foreach ($process as $type => $data) {
            if ($process::OUT === $type) {
                $this->info("\nRead from stdout: ".$data);
            } else { // $process::ERR === $type
                $this->info("\nRead from stderr: ".$data);
            }
        }
    }

    // check composer
    public function commandExists($commandName)
    {
        ob_start();
        passthru("command -v $commandName", $code);
        ob_end_clean();

        return (0 === $code) ? true : false;
    }

    // update data
    public function updateData(): bool
    {
        // modify config key
        $langArr = Language::where('table_name', 'configs')->where('table_key', 'account_cookie')->get();
        foreach ($langArr as $lang) {
            $lang->update([
                'table_key' => 'account_cookies',
            ]);
        }

        // modify lang key
        $langPackContents = Language::where('table_name', 'configs')->where('table_key', 'language_pack_contents')->get();
        foreach ($langPackContents as $packContent) {
            $content = (object) json_decode($packContent->lang_content, true);

            $newContent = ArrUtility::editKey($content, 'accountPoliciesCookie', 'accountPoliciesCookies');

            $packContent->update([
                'lang_content' => json_encode($newContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK),
            ]);
        }

        return true;
    }
}
