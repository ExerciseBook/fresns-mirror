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

    protected $path = 'upgrade';

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
        $this->download();
        //$this->composerInstall();
        //$this->migrate();

        return Command::SUCCESS;
    }

    public function download()
    {
        logger('upgrade:download');
        $client = new \GuzzleHttp\Client();

        $downloadUrl = 'http://fresns.liyu.wiki/fresns.zip';
        $filename = basename($downloadUrl);

        $path = \Storage::path($this->path);
        if (!file_exists($path)) {
            \File::makeDirectory($path, 0775, true);
        }

        $file = $path.'/'.$filename;

        $client->request('GET', $downloadUrl, [
            'sink' => $file,
        ]);

        $zip = \Zip::open($file);
        $extractPath = pathinfo($file)['filename'] ?? date('Y-m-d');
        $zip->extract(\Storage::path($this->path.'/'.$extractPath));
    }

    public function composerInstall()
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
    }

    public function migrate()
    {
        logger('upgrade:migrate');
        $this->call('migrate');
    }
}
