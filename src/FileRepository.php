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

use Countable;
use Illuminate\Cache\CacheManager;
use Illuminate\Container\Container;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Nwidart\Modules\Constants\ModuleEvent;
use Nwidart\Modules\Contracts\RepositoryInterface;
use Nwidart\Modules\Exceptions\InvalidAssetPath;
use Nwidart\Modules\Exceptions\ModuleNotFoundException;
use Nwidart\Modules\Process\Installer;
use Nwidart\Modules\Process\Updater;

abstract class FileRepository implements Countable, RepositoryInterface
{
    use Macroable;

    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application|\Laravel\Lumen\Application
     */
    protected $app;

    /**
     * The module path.
     *
     * @var string|null
     */
    protected $path;

    /**
     * The scanned paths.
     *
     * @var array
     */
    protected $paths = [];

    /**
     * @var string
     */
    protected $stubPath;

    /**
     * @var CacheManager
     */
    private $cache;

    /**
     * @var ConfigRepository
     */
    private $config;

    /**
     * @var Filesystem
     */
    private $files;

    private static $modules = [];

    /**
     * @var UrlGenerator
     */
    private $url;

    /**
     * The constructor.
     *
     * @param  string|null  $path
     */
    public function __construct(Container $app, $path = null)
    {
        $this->app = $app;
        $this->path = $path;
        $this->url = $app['url'];
        $this->config = $app['config'];
        $this->files = $app['files'];
        $this->cache = $app['cache'];
    }

    /**
     * Add other module location.
     *
     * @param  string  $path
     * @return $this
     */
    public function addLocation($path)
    {
        $this->paths[] = $path;

        return $this;
    }

    /**
     * Get all modules.
     */
    public function all(): array
    {
        return $this->scan();
    }

    /**
     * Get list of disabled modules.
     */
    public function allDisabled(): array
    {
        return $this->getByStatus(false);
    }

    /**
     * Get list of enabled modules.
     */
    public function allEnabled(): array
    {
        return $this->getByStatus(true);
    }

    /**
     * Get asset url from a specific module.
     *
     * @param  string  $asset
     *
     * @throws InvalidAssetPath
     */
    public function asset($asset): string
    {
        if (Str::contains($asset, ':') === false) {
            throw InvalidAssetPath::missingModuleName($asset);
        }
        [$name, $url] = explode(':', $asset);

        $baseUrl = str_replace(public_path() . DIRECTORY_SEPARATOR, '', $this->getAssetsPath());

        $url = $this->url->asset($baseUrl . "/{$name}/" . $url);

        return str_replace(['http://', 'https://'], '//', $url);
    }

    /**
     * {@inheritDoc}
     */
    public function assetPath(string $module): string
    {
        return $this->config('paths.assets') . '/' . $module;
    }

    /**
     * {@inheritDoc}
     */
    public function boot(): void
    {
        foreach ($this->getOrdered() as $module) {
            $module->boot();
        }
    }

    /**
     * Get all modules as laravel collection instance.
     */
    public function collections($status = 1): Collection
    {
        return new Collection($this->getByStatus($status));
    }

    /**
     * {@inheritDoc}
     */
    public function config(string $key, $default = null)
    {
        return $this->config->get('modules.' . $key, $default);
    }

    /**
     * Get count from all modules.
     */
    public function count(): int
    {
        return count($this->all());
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $name): bool
    {
        return $this->findOrFail($name)->delete();
    }

    /**
     * Disabling a specific module.
     *
     * @param  string  $name
     * @return void
     *
     * @throws \Nwidart\Modules\Exceptions\ModuleNotFoundException
     */
    public function disable($name)
    {
        $this->findOrFail($name)->disable();
    }

    /**
     * Enabling a specific module.
     *
     * @param  string  $name
     * @return void
     *
     * @throws \Nwidart\Modules\Exceptions\ModuleNotFoundException
     */
    public function enable($name)
    {
        $this->findOrFail($name)->enable();
    }

    /**
     * {@inheritDoc}
     */
    public function find(string $name)
    {
        return $this->all()[strtolower($name)] ?? null;
    }

    /**
     * Find a specific module, if there return that, otherwise throw exception.
     *
     *
     * @return Module
     *
     * @throws ModuleNotFoundException
     */
    public function findOrFail(string $name)
    {
        $module = $this->find($name);

        if ($module !== null) {
            return $module;
        }

        throw new ModuleNotFoundException("Module [{$name}] does not exist!");
    }

    /**
     * Forget the module used for cli session.
     */
    public function forgetUsed()
    {
        if ($this->getFiles()->exists($this->getUsedStoragePath())) {
            $this->getFiles()->delete($this->getUsedStoragePath());
        }
    }

    /**
     * Get module assets path.
     */
    public function getAssetsPath(): string
    {
        return $this->config('paths.assets');
    }

    /**
     * Get modules by status.
     */
    public function getByStatus($status): array
    {
        $modules = [];

        /** @var Module $module */
        foreach ($this->all() as $name => $module) {
            if ($module->isStatus($status)) {
                $modules[$name] = $module;
            }
        }

        return $modules;
    }

    /**
     * Get laravel filesystem instance.
     */
    public function getFiles(): Filesystem
    {
        return $this->files;
    }

    /**
     * Get module path for a specific module.
     *
     *
     * @return string
     */
    public function getModulePath($module)
    {
        try {
            return $this->findOrFail($module)->getPath() . '/';
        } catch (ModuleNotFoundException $e) {
            return $this->getPath() . '/' . Str::studly($module) . '/';
        }
    }

    /**
     * Get all ordered modules.
     *
     * @param  string  $direction
     */
    public function getOrdered($direction = 'asc'): array
    {
        $modules = $this->allEnabled();

        uasort($modules, function (Module $a, Module $b) use ($direction) {
            if ($a->get('priority') === $b->get('priority')) {
                return 0;
            }

            if ($direction === 'desc') {
                return $a->get('priority') < $b->get('priority') ? 1 : -1;
            }

            return $a->get('priority') > $b->get('priority') ? 1 : -1;
        });

        return $modules;
    }

    /**
     * {@inheritDoc}
     */
    public function getPath(): string
    {
        return $this->path ?: $this->config('paths.modules', base_path('Modules'));
    }

    /**
     * Get all additional paths.
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * Get scanned modules paths.
     */
    public function getScanPaths(): array
    {
        $paths = $this->paths;

        $paths[] = $this->getPath();

        if ($this->config('scan.enabled')) {
            $paths = array_merge($paths, $this->config('scan.paths'));
        }

        $paths = array_map(function ($path) {
            return Str::endsWith($path, '/*') ? $path : Str::finish($path, '/*');
        }, $paths);

        return $paths;
    }

    /**
     * Get stub path.
     *
     * @return string|null
     */
    public function getStubPath()
    {
        if ($this->stubPath !== null) {
            return $this->stubPath;
        }

        if ($this->config('stubs.enabled') === true) {
            return $this->config('stubs.path');
        }

        return $this->stubPath;
    }

    /**
     * Get module used for cli session.
     *
     * @throws \Nwidart\Modules\Exceptions\ModuleNotFoundException
     */
    public function getUsedNow(): string
    {
        return $this->findOrFail($this->getFiles()->get($this->getUsedStoragePath()));
    }

    /**
     * Get storage path for module used.
     */
    public function getUsedStoragePath(): string
    {
        $directory = storage_path('app/modules');
        if ($this->getFiles()->exists($directory) === false) {
            $this->getFiles()->makeDirectory($directory, 0777, true);
        }

        $path = storage_path('app/modules/modules.used');
        if (!$this->getFiles()->exists($path)) {
            $this->getFiles()->put($path, '');
        }

        return $path;
    }

    /**
     * Determine whether the given module exist.
     */
    public function has($name): bool
    {
        return array_key_exists(strtolower($name), $this->all());
    }

    /**
     * Install the specified module.
     *
     * @param  string  $name
     * @param  string  $version
     * @param  string  $type
     * @param  bool  $subtree
     * @return \Symfony\Component\Process\Process
     */
    public function install($name, $version = 'dev-master', $type = 'composer', $subtree = false)
    {
        $installer = new Installer($name, $version, $type, $subtree);

        return $installer->run();
    }

    /**
     * {@inheritDoc}
     */
    public function isDisabled(string $name): bool
    {
        return !$this->isEnabled($name);
    }

    /**
     * {@inheritDoc}
     */
    public function isEnabled(string $name): bool
    {
        return $this->findOrFail($name)->isEnabled();
    }

    /**
     * {@inheritDoc}
     */
    public function register(): void
    {
        foreach ($this->getOrdered() as $module) {
            $module->register();
        }
    }

    public function resetModules(): static
    {
        self::$modules = [];

        return $this;
    }

    /**
     * Get & scan all modules.
     *
     * @return array
     */
    public function scan()
    {
        if (!empty(self::$modules) && !$this->app->runningUnitTests()) {
            return self::$modules;
        }

        $paths = $this->getScanPaths();

        $modules = [];

        foreach ($paths as $key => $path) {
            $manifests = $this->getFiles()->glob("{$path}/module.json");

            is_array($manifests) || $manifests = [];

            foreach ($manifests as $manifest) {
                $json = Json::make($manifest);
                $name = $json->get('name');

                $modules[strtolower($name)] = $this->createModule($this->app, $name, dirname($manifest));
            }
        }

        self::$modules = $modules;

        return self::$modules;
    }

    /**
     * Set stub path.
     *
     * @param  string  $stubPath
     * @return $this
     */
    public function setStubPath($stubPath)
    {
        $this->stubPath = $stubPath;

        return $this;
    }

    /**
     * Set module used for cli session.
     *
     *
     * @throws ModuleNotFoundException
     */
    public function setUsed($name)
    {
        $module = $this->findOrFail($name);

        $this->getFiles()->put($this->getUsedStoragePath(), $module);

        $module->fireEvent(ModuleEvent::USED);
    }

    /**
     * Get all modules as collection instance.
     */
    public function toCollection(): Collection
    {
        return new Collection($this->scan());
    }

    /**
     * Update dependencies for the specified module.
     *
     * @param  string  $module
     */
    public function update($module)
    {
        with(new Updater($this))->update($module);
    }

    /**
     * Creates a new Module instance
     *
     * @param  Container  $app
     * @param  string  $args
     * @param  string  $path
     * @return \Nwidart\Modules\Module
     */
    abstract protected function createModule(...$args);
}
