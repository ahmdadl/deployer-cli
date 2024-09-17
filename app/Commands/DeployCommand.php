<?php

namespace App\Commands;

use App\LocalConfig;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Http;
use LaravelZero\Framework\Commands\Command;

use function Termwind\render;

class DeployCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:deploy
                            {appName? : name of the app in deployer repo config file}
                            {--default : deploy to default app}';

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
        $this->info('BEGIN DEPLOY ------');

        $config = app(LocalConfig::class);

        $appName = $this->argument('appName');
        if (empty($appName)) {
            if ($this->option('default')) {
                $appName = $config->defaultApp;
            } else {
                $appName = $this->ask('Please enter the name of the app or alias');
            }
        }


        // check if app exists
        if (!$app = $config->getApps()->first(
            fn($app) => $app->name === $appName || $app->alias === $appName
        )) {
            $this->error('App not found: ' . $appName);
            exit(1);
        }

        $appName = $app->name;

        $this->info('Deploying: ' . $appName . ' ........');

        $url = config('app.deployer_url') . '/' . $appName;

        $response = Http::post($url);

        render(<<<"HTML"
            <div class="py-1 ml-2">
                <div class="px-1 bg-green-300 text-black">Deployed to {$appName} successfully</div>
                <br />
                <ul>
                    <li>Status: <span class="bg-green-700 text-white mb-1">{$response->status()}</span></li>
                    <li>URL: <span class="bg-green-700 text-white mb-1">{$url}</span></li>
                    <li>Body: <span class="bg-green-700 text-white">{$response->body()}</span></li>
                </ul>
            </div>
        HTML);
    }
}
