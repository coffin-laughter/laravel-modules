<?php
/**
 *  +-------------------------------------------------------------------------------------------
 *  | Coffin [ 花开不同赏，花落不同悲。欲问相思处，花开花落时。 ]
 *  +-------------------------------------------------------------------------------------------
 *  | This is not a free software, without any authorization is not allowed to use and spread.
 *  +-------------------------------------------------------------------------------------------
 *  | Copyright (c) 2006~2024 All rights reserved.
 *  +-------------------------------------------------------------------------------------------
 *  | @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
 *  +-------------------------------------------------------------------------------------------
 */

namespace Nwidart\Modules;

use Exception;
use Illuminate\Filesystem\Filesystem;

class ModuleManifest
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    public $files;

    /**
     * The loaded manifest array.
     *
     * @var array
     */
    public $manifest;

    /**
     * The manifest path.
     *
     * @var string|null
     */
    public $manifestPath;

    /**
     * The base path.
     *
     * @var string
     */
    public $paths;

    /**
     * Create a new package manifest instance.
     *
     * @param  \Illuminate\Support\Collection  $paths
     * @param  string  $manifestPath
     * @return void
     */
    public function __construct(Filesystem $files, $paths, $manifestPath)
    {
        $this->files = $files;
        $this->paths = collect($paths);
        $this->manifestPath = $manifestPath;
    }

    /**
     * Get all of the aliases for all packages.
     *
     * @return array
     */
    public function aliases()
    {
        return $this->config('aliases');
    }

    /**
     * Build the manifest and write it to disk.
     *
     * @return void
     */
    public function build()
    {
        $providers = $this->paths
            ->flatMap(function ($path) {
                $manifests = $this->files->glob("{$path}/module.json");
                is_array($manifests) || $manifests = [];

                return collect($manifests)
                    ->map(function ($manifest) {
                        return $this->files->json($manifest);
                    });
            })
            ->sortBy(fn ($module) => $module['priority'] ?? 0)
            ->pluck('providers')
            ->flatten()
            ->filter()
            ->toArray();

        $this->write(
            [
                'providers' => $providers,
                'eager'     => $providers,
                'deferred'  => [],
            ]
        );
    }

    /**
     * Get all of the values for all packages for the given configuration name.
     *
     * @param  string  $key
     * @return array
     */
    public function config($key)
    {
        return collect($this->getManifest())->flatMap(function ($configuration) use ($key) {
            return (array) ($configuration[$key] ?? []);
        })->filter()->all();
    }

    /**
     * Get all of the service provider class names for all packages.
     *
     * @return array
     */
    public function providers()
    {
        return $this->config('providers');
    }

    /**
     * Get all of the service provider class names for all packages.
     *
     * @return array
     */
    public function providersArray()
    {
        return $this->getManifest()['providers'] ?? [];
    }

    /**
     * Get the current package manifest.
     *
     * @return array
     */
    protected function getManifest()
    {
        if (!is_null($this->manifest)) {
            return $this->manifest;
        }

        if (!is_file($this->manifestPath)) {
            $this->build();
        }

        return $this->manifest = is_file($this->manifestPath) ?
            $this->files->getRequire($this->manifestPath) : [];
    }

    /**
     * Write the given manifest array to disk.
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function write(array $manifest)
    {
        if (!is_writable($dirname = dirname($this->manifestPath))) {
            throw new Exception("The {$dirname} directory must be present and writable.");
        }
        $this->files->replace(
            $this->manifestPath,
            '<?php return ' . var_export($manifest, true) . ';'
        );
    }
}
