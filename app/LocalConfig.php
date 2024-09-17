<?php

namespace App;

use App\DataObjects\AppDataObject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class LocalConfig
{
    public string $defaultApp = '';
    public bool $fileExists = false;

    private Collection $apps;
    private string $configFileKey = 'app.local_file_name';

    /**
     * load file contents
     */
    public function load(): self
    {
        $configFile = $this->getConfigFile();
        if (!$configFile->exists) {
            $this->createFile([]);
            return $this;
        }

        $config = json_decode($configFile->contents, true);

        $this->defaultApp = $config['default'] ?? '';
        $this->apps = collect($config['apps'] ?? [])->map(fn($item) => new AppDataObject($item['name'], $item['alias']));
        $this->fileExists = true;

        return $this;
    }

    /**
     * get available apps
     *
     * @return Collection<AppDataObject>
     */
    public function getApps(): Collection
    {
        return $this->apps;
    }

    /**
     * create file for first time usage
     */
    public function createFile(array $contents): void
    {
        $configFile = $this->getConfigFile();
        if ($configFile->exists) {
            throw new \Exception('Local config file (' . config($this->configFileKey) . ') already exists');
        }

        Storage::disk('current')->put(config($this->configFileKey), json_encode($contents, JSON_PRETTY_PRINT));

        $this->refresh();
    }

    /**
     * save file after changes
     */
    public function save(): void
    {
        $contents = [
            'default' => $this->defaultApp,
            'apps' => $this->apps
        ];

        Storage::disk('current')->put(config($this->configFileKey), json_encode($contents, JSON_PRETTY_PRINT));

        $this->refresh();
    }

    /**
     * add new app
     */
    public function addApp(string $name, ?string $alias = null): self
    {
        // check if app already exists
        if ($this->apps->contains(fn(AppDataObject $app) => $app->name === $name)) {
            throw new \Exception('App ' . $name . ' already exists');
        }

        $this->apps->push(new AppDataObject($name, $alias));
        $this->save();

        return $this;
    }

    /**
     * edit app details
     */
    public function editApp(string $name, ?string $alias = null): self
    {
        $this->apps = $this->apps->map(fn(AppDataObject $app) => $app->name === $name ? new AppDataObject($name, $alias) : $app);
        $this->save();

        return $this;
    }

    /**
     * delete app
     */
    public function deleteApp(string $name): self
    {
        $this->apps = $this->apps->filter(fn(AppDataObject $app) => $app->name !== $name);
        $this->save();

        return $this;
    }

    /**
     * reload file from disk
     */
    public function refresh(): self
    {
        return $this->load();
    }

    /**
     * The getConfigFile function returns an object with information about the existence and contents
     * of a specified file.
     * 
     * @return object<{object,contents}>
     */
    private function getConfigFile(): object
    {
        return (object) [
            'exists' => Storage::disk('current')->exists(config($this->configFileKey)),
            'contents' => Storage::disk('current')->get(config($this->configFileKey))
        ];
    }
}
