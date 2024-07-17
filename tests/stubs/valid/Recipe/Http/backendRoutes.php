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
use Illuminate\Routing\Router;

/** @var Router $router */
$router->group(['prefix' => '/recipe'], function (Router $router) {
    $router->bind('recipes', function ($id) {
        return app('Modules\Recipe\Repositories\RecipeRepository')->find($id);
    });
    $router->resource('recipes', 'RecipeController', ['except' => ['show'], 'names' => [
        'index'   => 'admin.recipe.recipe.index',
        'create'  => 'admin.recipe.recipe.create',
        'store'   => 'admin.recipe.recipe.store',
        'edit'    => 'admin.recipe.recipe.edit',
        'update'  => 'admin.recipe.recipe.update',
        'destroy' => 'admin.recipe.recipe.destroy',
    ]]);
    // append
});
