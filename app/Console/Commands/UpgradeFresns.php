<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

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
        logger('upgrade:composer install');
        $process = new Process(['composer', 'install'], base_path());
        $process->start();

        foreach ($process as $type => $data) {
            if ($process::OUT === $type) {
                $this->info("\nRead from stdout: ".$data);
            } else { // $process::ERR === $type
                $this->info("\nRead from stderr: ".$data);
            }
        }

        logger('upgrade:migrate');
        $this->call('migrate');

        return Command::SUCCESS;
    }
}
