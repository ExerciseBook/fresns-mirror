<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Console\Commands\Upgrade;

use Illuminate\Console\Command;

class Upgrade13Command extends Command
{
    protected $signature = 'fresns:upgrade-13';

    protected $description = 'upgrade to fresns v2.3.0';

    public function __construct()
    {
        parent::__construct();
    }

    // execute the console command
    public function handle()
    {
        logger('upgrade:fresns-13 migrate');
        $this->call('migrate', ['--force' => true]);

        return Command::SUCCESS;
    }
}
