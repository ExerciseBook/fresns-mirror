<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Console\Commands\Upgrade;

use App\Models\PostAppend;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class Upgrade6Command extends Command
{
    protected $signature = 'fresns:upgrade-8';

    protected $description = 'upgrade to fresns v2.0.0-beta.8';

    public function __construct()
    {
        parent::__construct();
    }

    // execute the console command
    public function handle()
    {
        logger('upgrade:fresns-8 composerInstall');
        $this->composerInstall();

        logger('upgrade:fresns-8 migrate');
        $this->call('migrate', ['--force' => true]);

        logger('upgrade:fresns-8 updateData');
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
        $postAppends = PostAppend::get();
        foreach ($postAppends as $append) {
            $isAllow = $append->is_allow ? 0 : 1;

            $append->update([
                'is_allow' => $isAllow,
            ]);
        }

        return true;
    }
}
