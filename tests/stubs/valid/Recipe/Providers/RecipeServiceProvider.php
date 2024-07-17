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

namespace Modules\Recipe\Providers;

use Illuminate\Support\ServiceProvider;

class RecipeServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();
    }

    private function registerBindings()
    {
        $this->app->bind(
            'Modules\Recipe\Repositories\RecipeRepository',
            function () {
                $repository = new \Modules\Recipe\Repositories\Eloquent\EloquentRecipeRepository(new \Modules\Recipe\Entities\Recipe());

                if (!config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Recipe\Repositories\Cache\CacheRecipeDecorator($repository);
            }
        );
        // add bindings
    }
}
