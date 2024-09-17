<?php

namespace App\Commands;

use App\LocalConfig;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class DeployCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:deploy {appName? : name of the app in deployer repo config file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call remote deployer with this app name';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Deploying...');

        $config = app(LocalConfig::class);

        $appName = $this->argument('appName');
        if (empty($appName)) {
            $appName = $config->defaultApp;

            dd($appName);
        }
    }
}
