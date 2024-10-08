<?php

namespace App\Commands;

use App\LocalConfig;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

use function Termwind\render;

class InitiateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:init
                            {app? : name of the repo in deployer config file}
                            {alias? : alias of the app name to be used locally}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'init deployer in current directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $appName = $this->argument('app');
        $alias = $this->argument('alias') ?? $appName;

        if (empty($appName)) {
            $appName = $this->ask('Please enter the name of the app');
            $alias = $this->askWithCompletion('Please enter the alias of the app', [$appName], $appName);
        }

        /** @var LocalConfig $localConfig */
        $localConfig = app(LocalConfig::class);
        if ($localConfig->fileExists && $localConfig->getApps()->isNotEmpty()) {
            $this->error('deployer.json already exists');
            exit(1);
        }

        $localConfig->defaultApp = $appName;
        $localConfig->addApp($appName, $alias);
        $localConfig->save();

        render(<<<"HTML"
            <div class="py-1 ml-2">
                <div class="px-1 bg-green-300 text-black">Deployer Initiated successfully</div>
                <br />
                <ul>
                    <li>App: <span class="bg-green-700 text-white mb-1">$appName</span></li>
                    <li>Alias: <span class="bg-green-700 text-white">$alias</span></li>
                </ul>
            </div>
        HTML);
    }
}
