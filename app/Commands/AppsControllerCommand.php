<?php

namespace App\Commands;

use App\DataObjects\AppDataObject;
use App\LocalConfig;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class AppsControllerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:apps
                            {cmd? : list, add, edit, del, default}
                            {--app= : name of the app in deployer config file}
                            {--alias= : alias of the app name to be used locally}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Control apps in deployer config file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (empty($command = $this->argument('cmd'))) {
            $this->info('Available commands: list, add, del');
            exit(0);
        }

        /** @var LocalConfig $localConfig */
        $localConfig = app(LocalConfig::class);

        match ($command) {
            'list' => $this->listApps($localConfig),
            'add' => $this->addApp($localConfig),
            'edit' => $this->editApp($localConfig),
            'del' => $this->delApp($localConfig),
            'default' => $this->setDefaultApp($localConfig),
            default => $this->listApps($localConfig),
        };
    }

    /**
     * list all apps
     */
    private function listApps(LocalConfig $localConfig): void
    {
        $this->table(
            ['Name', 'Alias'],
            $localConfig->getApps()->map(fn(AppDataObject $app) => $app->setIsDefault($localConfig->defaultApp === $app->name)->toArray())
        );
    }

    /**
     * add new app
     */
    private function addApp(LocalConfig $localConfig): void
    {
        $this->info('Add app');
        $appName = $this->option('app');
        $alias = $this->option('alias') ?? $appName;

        if (empty($appName)) {
            $appName = $this->ask('Please enter the name of the app');
            $alias = $this->askWithCompletion('Please enter the alias of the app', [$appName], $appName);
        }

        $localConfig->addApp($appName, $alias);
        $localConfig->save();

        $this->listApps($localConfig);
    }

    /**
     * delete an app
     */
    private function delApp(LocalConfig $localConfig): void
    {
        $this->info('Delete app');
        $appName = $this->option('app');
        if (empty($appName)) {
            $appName = $this->ask('Please enter the name of the app');
        }
        $localConfig->deleteApp($appName);
        $localConfig->save();

        $this->listApps($localConfig);
    }

    /**
     * edit an app
     */
    private function editApp(LocalConfig $localConfig): void
    {
        $this->info('Edit app');
        $appName = $this->option('app');
        $alias = $this->option('alias');
        if (empty($appName)) {
            $appName = $this->ask('Please enter the name of the app');
            $alias = $this->askWithCompletion('Please enter the alias of the app', [$appName], $appName);
        }
        $localConfig->editApp($appName, $alias);
        $localConfig->save();

        $this->listApps($localConfig);
    }

    /**
     * set default app
     */
    private function setDefaultApp(LocalConfig $localConfig): void
    {
        $this->info('Set default app');
        $appName = $this->option('app');
        if (empty($appName)) {
            $appName = $this->ask('Please enter the name of the app');
        }
        $localConfig->defaultApp = $appName;
        $localConfig->save();

        $this->listApps($localConfig);
    }
}
