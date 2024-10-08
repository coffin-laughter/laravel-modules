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

use Composer\InstalledVersions;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Nwidart\Modules\Constants\ModuleEvent;
use Nwidart\Modules\Contracts\RepositoryInterface;
use Nwidart\Modules\Exceptions\Handler;
use Nwidart\Modules\Exceptions\InvalidActivatorClass;
use Nwidart\Modules\Support\Db\Query;
use Nwidart\Modules\Support\Macros\MacrosRegister;
use Nwidart\Modules\Support\Sanctum\PersonalAccessToken;
use Nwidart\Modules\Support\Stub;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Output\NullOutput;

class LaravelModulesServiceProvider extends ModulesServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot(): void
    {
        $this->registerNamespaces();

        $this->app->singleton(
            ModuleManifest::class,
            fn () => new ModuleManifest(
                new Filesystem(),
                app(Contracts\RepositoryInterface::class)->getScanPaths(),
                $this->getCachedModulePath()
            )
        );

        $this->registerModules();

        $this->registerEvents();

        try {
            $this->listenDBLog();
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        }

        try {
            $this->app->make(MacrosRegister::class)->boot();
        } catch (BindingResolutionException $e) {
        }

        $this->registerSanctumCache();

        AboutCommand::add('Laravel-Modules', [
            'Version' => fn () => InstalledVersions::getPrettyVersion('coffin-laughter/laravel-modules'),
        ]);
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->registerServices();
        $this->setupStubPath();
        $this->registerProviders();
        $this->registerExceptionHandler();
        $this->registerMigrations();

        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'modules');
    }

    public function registerSanctumCache(): void
    {
        if (class_exists(\Laravel\Sanctum\Sanctum::class) && $this->app['config']->get('modules.auth_sanctum_cache')) {
            \Laravel\Sanctum\Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        }
    }

    /**
     * Setup stub path.
     */
    public function setupStubPath(): void
    {
        $path = $this->app['config']->get('modules.stubs.path') ?? __DIR__ . '/Commands/stubs';
        Stub::setBasePath($path);

        $this->app->booted(function ($app) {
            /** @var RepositoryInterface $moduleRepository */
            $moduleRepository = $app[RepositoryInterface::class];
            if ($moduleRepository->config('stubs.enabled') === true) {
                Stub::setBasePath($moduleRepository->config('stubs.path'));
            }
        });
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     *
     * @time  : 2024-05-23 上午10:07
     */
    protected function listenDBLog(): void
    {
        if ($this->app['config']->get('modules.listen_db_log')) {
            Query::listen();

            $this->app->terminating(function () {
                Query::log();
            });
        }
    }

    /**
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     *
     * @time  : 2024-05-23 上午10:07
     */
    protected function registerExceptionHandler(): void
    {
        if (isRequestFromAjax()) {
            $this->app->singleton(ExceptionHandler::class, Handler::class);
        }
    }

    protected function registerMigrations(): void
    {
        if (!$this->app['config']->get('modules.auto-discover.migrations', true)) {
            return;
        }

        $this->app->resolving(Migrator::class, function (Migrator $migrator) {
            $path = implode(DIRECTORY_SEPARATOR, [
                $this->app['config']->get('modules.paths.modules'),
                '*',
                '[Dd]atabase',
                'migrations',
            ]);

            collect(glob($path, GLOB_ONLYDIR))
                ->each(function (string $path) use ($migrator) {
                    $migrator->path($path);
                });
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function registerServices(): void
    {
        $this->app->singleton(Contracts\RepositoryInterface::class, function ($app) {
            $path = $app['config']->get('modules.paths.modules');

            return new Laravel\LaravelFileRepository($app, $path);
        });
        $this->app->singleton(Contracts\ActivatorInterface::class, function ($app) {
            $activator = $app['config']->get('modules.activator');
            $class = $app['config']->get('modules.activators.' . $activator)['class'];

            if ($class === null) {
                throw InvalidActivatorClass::missingConfig();
            }

            return new $class($app);
        });
        $this->app->alias(Contracts\RepositoryInterface::class, 'modules');
    }

    /**
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-09-02 10:47
     */
    private function registerEvents(): void
    {
        Event::listen(RequestHandled::class, config('modules.response.request_handled_listener'));
        Event::listen(
            [
                'modules.*.' . ModuleEvent::DELETED,
                'modules.*.' . ModuleEvent::CREATED,
                'modules.*.' . ModuleEvent::DISABLED,
                'modules.*.' . ModuleEvent::ENABLED,
            ],
            fn () => Artisan::call('module:clear-compiled', outputBuffer: new NullOutput())
        );
    }
}
