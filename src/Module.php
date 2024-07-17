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

use Illuminate\Cache\CacheManager;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Translation\Translator;
use Nwidart\Modules\Contracts\ActivatorInterface;

abstract class Module
{
    use Macroable;

    /**
     * The laravel|lumen application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application|\Laravel\Lumen\Application
     */
    protected $app;

    /**
     * @var array of cached Json objects, keyed by filename
     */
    protected $moduleJson = [];

    /**
     * The module name.
     */
    protected $name;

    /**
     * The module path.
     *
     * @var string
     */
    protected $path;
    /**
     * @var ActivatorInterface
     */
    private $activator;

    /**
     * @var CacheManager
     */
    private $cache;

    /**
     * @var Filesystem
     */
    private $files;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * The constructor.
     */
    public function __construct(Container $app, string $name, $path)
    {
        $this->name = $name;
        $this->path = $path;
        $this->cache = $app['cache'];
        $this->files = $app['files'];
        $this->translator = $app['translator'];
        $this->activator = $app[ActivatorInterface::class];
        $this->app = $app;
    }

    /**
     * Handle call __toString.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getStudlyName();
    }

    /**
     * Delete the current module.
     */
    public function delete(): bool
    {
        $this->activator->delete($this);

        return $this->json()->getFilesystem()->deleteDirectory($this->getPath());
    }

    /**
     * Disable the current module.
     */
    public function disable(): void
    {
        $this->fireEvent('disabling');

        $this->activator->disable($this);
        $this->flushCache();

        $this->fireEvent('disabled');
    }

    /**
     * Enable the current module.
     */
    public function enable(): void
    {
        $this->fireEvent('enabling');

        $this->activator->enable($this);
        $this->flushCache();

        $this->fireEvent('enabled');
    }

    /**
     * Get a specific data from json file by given the key.
     *
     * @param  null  $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->json()->get($key, $default);
    }

    /**
     * Get app path.
     */
    public function getAppPath(): string
    {
        $app_path = rtrim($this->getExtraPath(config('modules.paths.app_folder', '')), '/');

        return is_dir($app_path) ? $app_path : $this->getPath();
    }

    /**
     * Returns an array of assets
     */
    public static function getAssets(): array
    {
        $paths = [];

        if (file_exists(public_path('build/manifest.json'))) {
            $files = json_decode(file_get_contents(public_path('build/manifest.json')), true);

            if (is_array($files)) {
                foreach ($files as $file) {
                    // Ignore files which aren't entrypoints.
                    if (empty($file['isEntry'])) {
                        continue;
                    }

                    if (isset($file['src'])) {
                        $paths[] = $file['src'];
                    }
                }
            }
        }

        return $paths;
    }

    /**
     * Get the path to the cached *_module.php file.
     */
    abstract public function getCachedServicesPath(): string;

    /**
     * Get a specific data from composer.json file by given the key.
     *
     * @param  null  $default
     * @return mixed
     */
    public function getComposerAttr($key, $default = null)
    {
        return $this->json('composer.json')->get($key, $default);
    }

    /**
     * Get description.
     */
    public function getDescription(): string
    {
        return $this->get('description');
    }

    /**
     * Get name in lower case.
     */
    public function getLowerName(): string
    {
        return strtolower($this->name);
    }

    /**
     * Get name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get path.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get priority.
     */
    public function getPriority(): string
    {
        return $this->get('priority');
    }

    /**
     * Get name in snake case.
     */
    public function getSnakeName(): string
    {
        return Str::snake($this->name);
    }

    /**
     * Get name in studly case.
     *
     * @return string
     */
    public function getStudlyName(): string
    {
        return Str::studly($this->name);
    }

    /**
     * Get name in studly case.
     */
    public function getStudlyName(): string
    {
        return Str::studly($this->name);
    }

    /**
     *  Determine whether the current module not disabled.
     */
    public function isDisabled(): bool
    {
        return !$this->isEnabled();
    }

    /**
     * Determine whether the current module activated.
     */
    public function isEnabled(): bool
    {
        return $this->activator->hasStatus($this, true);
    }

    /**
     * Determine whether the given status same with the current module status.
     */
    public function isStatus(bool $status): bool
    {
        return $this->activator->hasStatus($this, $status);
    }

    /**
     * Get json contents from the cache, setting as needed.
     *
     * @param  string  $file
     */
    public function json($file = null): Json
    {
        if ($file === null) {
            $file = 'module.json';
        }

        return Arr::get($this->moduleJson, $file, function () use ($file) {
            return $this->moduleJson[$file] = new Json($this->getPath() . '/' . $file, $this->files);
        });
    }

    /**
     * Register the module.
     */
    public function register(): void
    {
        $this->registerAliases();

        $this->registerProviders();

        if ($this->isLoadFilesOnBoot() === false) {
            $this->registerFiles();
        }

        $this->fireEvent('register');
    }

    /**
     * Register the aliases from this module.
     */
    abstract public function registerAliases(): void;

    /**
     * Register the service providers from this module.
     */
    abstract public function registerProviders(): void;

    /**
     * Set active state for current module.
     */
    public function setActive(bool $active): void
    {
        $this->activator->setActive($this, $active);
    }

    /**
     * Get extra path.
     */
    public function setPath($path): Module
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Set path.
     *
     * @param  string  $path
     * @return $this
     */
    public function setPath($path): Module
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Register the module event.
     *
     * @param string $event
     */
    protected function fireEvent($event): void
    {
        $this->app['events']->dispatch(sprintf('modules.%s.' . $event, $this->getLowerName()), [$this]);
    }

    /**
     * Register the module event.
     *
     * @param  string  $event
     */
    protected function fireEvent($event): void
    {
        $this->app['events']->dispatch(sprintf('modules.%s.' . $event, $this->getLowerName()), [$this]);
    }

    /**
     * Check if can load files of module on boot method.
     */
    protected function isLoadFilesOnBoot(): bool
    {
        return config('modules.register.files', 'register') === 'boot' &&
            // force register method if option == boot && app is AsgardCms
            !class_exists('\Modules\Core\Foundation\AsgardCms');
    }

    /**
     * Register the files from this module.
     */
    protected function registerFiles(): void
    {
        foreach ($this->get('files', []) as $file) {
            include $this->path . '/' . $file;
        }
    }

    /**
     * Register module's translation.
     */
    protected function registerTranslation(): void
    {
        $lowerName = $this->getLowerName();

        $langPath = $this->getPath() . '/Resources/lang';

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $lowerName);
        }
    }

    private function flushCache(): void
    {
        if (config('modules.cache.enabled')) {
            $this->cache->store(config('modules.cache.driver'))->flush();
        }
    }

    /**
     * Register a translation file namespace.
     */
    private function loadTranslationsFrom(string $path, string $namespace): void
    {
        $this->translator->addNamespace($namespace, $path);
    }
}
