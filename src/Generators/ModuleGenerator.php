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

namespace Nwidart\Modules\Generators;

use Illuminate\Config\Repository as Config;
use Illuminate\Console\Command as Console;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Nwidart\Modules\Contracts\ActivatorInterface;
use Nwidart\Modules\FileRepository;
use Nwidart\Modules\Support\Config\GenerateConfigReader;
use Nwidart\Modules\Support\Stub;
use Nwidart\Modules\Traits\PathNamespace;

class ModuleGenerator extends Generator
{
    use PathNamespace;

    /**
     * The activator instance
     *
     * @var ActivatorInterface
     */
    protected $activator;

    /**
     * Module author
     */
    protected array $author = [
        'name', 'email',
    ];

    /**
     * The laravel component Factory instance.
     *
     * @var \Illuminate\Console\View\Components\Factory
     */
    protected $component;

    /**
     * The laravel config instance.
     *
     * @var Config
     */
    protected $config;

    /**
     * The laravel console instance.
     *
     * @var Console
     */
    protected $console;

    /**
     * The laravel filesystem instance.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Force status.
     *
     * @var bool
     */
    protected $force = false;

    /**
     * Enables the module.
     *
     * @var bool
     */
    protected $isActive = false;

    /**
     * The module instance.
     *
     * @var \Nwidart\Modules\Module
     */
    protected $module;

    /**
     * The module name will created.
     *
     * @var string
     */
    protected $name;

    /**
     * set default module type.
     *
     * @var string
     */
    protected $type = 'web';

    /**
     * Vendor name
     */
    protected ?string $vendor = null;

    /**
     * The constructor.
     */
    public function __construct(
        $name,
        ?FileRepository $module = null,
        ?Config $config = null,
        ?Filesystem $filesystem = null,
        ?Console $console = null,
        ?ActivatorInterface $activator = null
    ) {
        $this->name = $name;
        $this->config = $config;
        $this->filesystem = $filesystem;
        $this->console = $console;
        $this->module = $module;
        $this->activator = $activator;
    }

    /**
     * Generate the module.
     */
    public function generate(): int
    {
        $name = $this->getName();

        if ($this->module->has($name)) {
            if ($this->force) {
                $this->module->delete($name);
            } else {
                $this->component->error("Module [{$name}] already exists!");

                return E_ERROR;
            }
        }
        $this->component->info("Creating module: [$name]");

        $this->generateFolders();

        $this->generateModuleJsonFile();

        if ($this->type !== 'plain') {
            $this->generateFiles();
            $this->generateResources();
        }

        if ($this->type === 'plain') {
            $this->cleanModuleJsonFile();
        }

        $this->activator->setActiveByName($name, $this->isActive);

        $this->console->newLine(1);

        $this->component->info("Module [{$name}] created successfully.");

        return 0;
    }

    /**
     * Generate the files.
     */
    public function generateFiles()
    {
        foreach ($this->getFiles() as $stub => $file) {
            $path = $this->module->getModulePath($this->getName()) . $file;

            $this->component->task("Generating file {$path}", function () use ($stub, $path) {
                if (!$this->filesystem->isDirectory($dir = dirname($path))) {
                    $this->filesystem->makeDirectory($dir, 0775, true);
                }

                $this->filesystem->put($path, $this->getStubContents($stub));
            });
        }
    }

    /**
     * Generate the folders.
     */
    public function generateFolders()
    {
        foreach ($this->getFolders() as $key => $folder) {
            $folder = GenerateConfigReader::read($key);

            if ($folder->generate() === false) {
                continue;
            }

            $path = $this->module->getModulePath($this->getName()) . '/' . $folder->getPath();

            $this->filesystem->ensureDirectoryExists($path, 0755, true);
            if (config('modules.stubs.gitkeep')) {
                $this->generateGitKeep($path);
            }
        }
    }

    /**
     * Generate git keep to the specified path.
     *
     * @param  string  $path
     */
    public function generateGitKeep($path)
    {
        $this->filesystem->put($path . '/.gitkeep', '');
    }

    /**
     * Generate some resources.
     */
    public function generateResources()
    {
        if (GenerateConfigReader::read('seeder')->generate() === true) {
            $this->console->call('module:make-seed', [
                'name'     => $this->getName(),
                'module'   => $this->getName(),
                '--master' => true,
            ]);
        }

        $providerGenerator = GenerateConfigReader::read('provider');
        if ($providerGenerator->generate() === true) {
            $this->console->call('module:make-provider', [
                'name'     => $this->getName() . 'ServiceProvider',
                'module'   => $this->getName(),
                '--master' => true,
            ]);
        } else {
            // delete register ServiceProvider on module.json
            $path = $this->module->getModulePath($this->getName()) . DIRECTORY_SEPARATOR . 'module.json';
            $module_file = $this->filesystem->get($path);
            $this->filesystem->put(
                $path,
                preg_replace('/"providers": \[.*?\],/s', '"providers": [ ],', $module_file)
            );
        }

        $eventGeneratorConfig = GenerateConfigReader::read('event-provider');
        if (
            (is_null($eventGeneratorConfig->getPath()) && $providerGenerator->generate())
            || (!is_null($eventGeneratorConfig->getPath()) && $eventGeneratorConfig->generate())
        ) {
            $this->console->call('module:make-event-provider', [
                'module' => $this->getName(),
            ]);
        } else {
            if ($providerGenerator->generate()) {
                // comment register EventServiceProvider
                $this->filesystem->replaceInFile(
                    '$this->app->register(Event',
                    '// $this->app->register(Event',
                    $this->module->getModulePath($this->getName()) . DIRECTORY_SEPARATOR . $providerGenerator->getPath() . DIRECTORY_SEPARATOR . sprintf('%sServiceProvider.php', $this->getName())
                );
            }
        }

        $routeGeneratorConfig = GenerateConfigReader::read('route-provider');
        if (
            (is_null($routeGeneratorConfig->getPath()) && $providerGenerator->generate())
            || (!is_null($routeGeneratorConfig->getPath()) && $routeGeneratorConfig->generate())
        ) {
            $this->console->call('module:route-provider', [
                'module' => $this->getName(),
            ]);
        } else {
            if ($providerGenerator->generate()) {
                // comment register RouteServiceProvider
                $this->filesystem->replaceInFile(
                    '$this->app->register(Route',
                    '// $this->app->register(Route',
                    $this->module->getModulePath($this->getName()) . DIRECTORY_SEPARATOR . $providerGenerator->getPath() . DIRECTORY_SEPARATOR . sprintf('%sServiceProvider.php', $this->getName())
                );
            }
        }

        if (GenerateConfigReader::read('controller')->generate() === true) {
            $options = $this->type == 'api' ? ['--api' => true] : [];
            $this->console->call('module:make-controller', [
                    'controller' => $this->getName() . 'Controller',
                    'module'     => $this->getName(),
                ] + $options);
        }
    }

    public function getComponent(): \Illuminate\Console\View\Components\Factory
    {
        return $this->component;
    }

    /**
     * Get the laravel config instance.
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get the laravel console instance.
     *
     * @return Console
     */
    public function getConsole()
    {
        return $this->console;
    }

    /**
     * Get the list of files will created.
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->module->config('stubs.files');
    }

    /**
     * Get the laravel filesystem instance.
     *
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * Get the list of folders will created.
     *
     * @return array
     */
    public function getFolders()
    {
        return $this->module->config('paths.generator');
    }

    /**
     * Get the module instance.
     *
     * @return \Nwidart\Modules\Module
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Get the name of module will created. By default in studly case.
     *
     * @return string
     */
    public function getName()
    {
        return Str::studly($this->name);
    }

    /**
     * get the list for the replacements.
     */
    public function getReplacements()
    {
        return $this->module->config('stubs.replacements');
    }

    /**
     * Set the modules activator
     *
     *
     * @return $this
     */
    public function setActivator(ActivatorInterface $activator)
    {
        $this->activator = $activator;

        return $this;
    }

    /**
     * Set active flag.
     *
     *
     * @return $this
     */
    public function setActive(bool $active)
    {
        $this->isActive = $active;

        return $this;
    }

    /**
     * Setting the author from the command
     *
     * @return $this
     */
    public function setAuthor(?string $name = null, ?string $email = null)
    {
        $this->author['name'] = $name;
        $this->author['email'] = $email;

        return $this;
    }

    public function setComponent(\Illuminate\Console\View\Components\Factory $component): self
    {
        $this->component = $component;

        return $this;
    }

    /**
     * Set the laravel config instance.
     *
     * @param  Config  $config
     * @return $this
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Set the laravel console instance.
     *
     * @param  Console  $console
     * @return $this
     */
    public function setConsole($console)
    {
        $this->console = $console;

        return $this;
    }

    /**
     * Set the laravel filesystem instance.
     *
     * @param  Filesystem  $filesystem
     * @return $this
     */
    public function setFilesystem($filesystem)
    {
        $this->filesystem = $filesystem;

        return $this;
    }

    /**
     * Set force status.
     *
     * @param  bool|int  $force
     * @return $this
     */
    public function setForce($force)
    {
        $this->force = $force;

        return $this;
    }

    /**
     * Set the module instance.
     *
     * @param  mixed  $module
     * @return $this
     */
    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Set type.
     *
     * @param  string  $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Installing vendor from the command
     *
     * @return $this
     */
    public function setVendor(?string $vendor = null)
    {
        $this->vendor = $vendor;

        return $this;
    }

    /**
     * Get replacement for $APP_FOLDER_NAME$.
     *
     * @return string
     */
    protected function getAppFolderNameReplacement()
    {
        return $this->module->config('paths.app_folder');
    }

    /**
     * Get replacement for $AUTHOR_EMAIL$.
     *
     * @return string
     */
    protected function getAuthorEmailReplacement()
    {
        return $this->author['email'] ?: $this->module->config('composer.author.email');
    }

    /**
     * Get replacement for $AUTHOR_NAME$.
     *
     * @return string
     */
    protected function getAuthorNameReplacement()
    {
        return $this->author['name'] ?: $this->module->config('composer.author.name');
    }

    /**
     * Get the module name in lower case.
     *
     * @return string
     */
    protected function getLowerNameReplacement()
    {
        return strtolower($this->getName());
    }

    /**
     * Get replacement for $MODULE_NAMESPACE$.
     *
     * @return string
     */
    protected function getModuleNamespaceReplacement()
    {
        return str_replace('\\', '\\\\', $this->module->config('namespace') ?? $this->path_namespace($this->module->config('paths.modules')));
    }

    protected function getProviderNamespaceReplacement(): string
    {
        return str_replace('\\', '\\\\', GenerateConfigReader::read('provider')->getNamespace());
    }

    /**
     * Get array replacement for the specified stub.
     *
     *
     * @return array
     */
    protected function getReplacement($stub)
    {
        $replacements = $this->module->config('stubs.replacements');

        if (!isset($replacements['composer']['APP_FOLDER_NAME'])) {
            $replacements['composer'][] = 'APP_FOLDER_NAME';
        }

        if (!isset($replacements[$stub])) {
            return [];
        }

        $keys = $replacements[$stub];

        $replaces = [];

        if ($stub === 'json' || $stub === 'composer') {
            if (in_array('PROVIDER_NAMESPACE', $keys, true) === false) {
                $keys[] = 'PROVIDER_NAMESPACE';
            }
        }
        foreach ($keys as $key) {
            if (method_exists($this, $method = 'get' . ucfirst(Str::studly(strtolower($key))) . 'Replacement')) {
                $replaces[$key] = $this->$method();
            } else {
                $replaces[$key] = null;
            }
        }

        return $replaces;
    }

    /**
     * Get the contents of the specified stub file by given stub name.
     *
     *
     * @return string
     */
    protected function getStubContents($stub)
    {
        return (new Stub(
            '/' . $stub . '.stub',
            $this->getReplacement($stub)
        )
        )->render();
    }

    /**
     * Get the module name in studly case.
     *
     * @return string
     */
    protected function getStudlyNameReplacement()
    {
        return $this->getName();
    }

    /**
     * Get replacement for $VENDOR$.
     *
     * @return string
     */
    protected function getVendorReplacement()
    {
        return $this->vendor ?: $this->module->config('composer.vendor');
    }

    /**
     * Remove the default service provider that was added in the module.json file
     * This is needed when a --plain module was created
     */
    private function cleanModuleJsonFile()
    {
        $path = $this->module->getModulePath($this->getName()) . 'module.json';

        $content = $this->filesystem->get($path);
        $namespace = $this->getModuleNamespaceReplacement();
        $studlyName = $this->getStudlyNameReplacement();

        $provider = '"' . $namespace . '\\\\' . $studlyName . '\\\\Providers\\\\' . $studlyName . 'ServiceProvider"';

        $content = str_replace($provider, '', $content);

        $this->filesystem->put($path, $content);
    }

    /**
     * Generate the module.json file
     */
    private function generateModuleJsonFile()
    {
        $path = $this->module->getModulePath($this->getName()) . 'module.json';

        $this->component->task("Generating file $path", function () use ($path) {
            if (!$this->filesystem->isDirectory($dir = dirname($path))) {
                $this->filesystem->makeDirectory($dir, 0775, true);
            }

            $this->filesystem->put($path, $this->getStubContents('json'));
        });
    }

    /**
     * Get replacement for $CONTROLLER_NAMESPACE$.
     */
    private function getControllerNamespaceReplacement(): string
    {
        if ($this->module->config('paths.generator.controller.namespace')) {
            return $this->module->config('paths.generator.controller.namespace');
        } else {
            return $this->path_namespace(ltrim($this->module->config('paths.generator.controller.path', 'app/Http/Controllers'), config('modules.paths.app_folder')));
        }
    }
}
