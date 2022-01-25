<?php

namespace App\Fresns\Panel\Console\Commands;

use Illuminate\Console\Command;

class UpgradeFresns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fresns:upgrade';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'upgrade fresns';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        echo 123;
    }
}
