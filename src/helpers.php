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
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Vite;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Vite as ViteFacade;

if (!function_exists('module_path')) {
    function module_path($name, $path = ''): string
    {
        $module = app('modules')->find($name);

        return $module->getPath() . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param string $path
     * @return string
     */
    function config_path(string $path = ''): string
    {
        return app()->basePath() . '/config' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('public_path')) {
    /**
     * @param string $path
     * @return string
     * @throws BindingResolutionException
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-29 下午3:01
     */
    function public_path(string $path = ''): string
    {
        return app()->make('path.public') . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : $path);
    }
}

if (!function_exists('module_vite')) {
    /**
     * support for vite
     */
    function module_vite($module, $asset): Vite
    {
        return ViteFacade::useHotFile(storage_path('vite.hot'))->useBuildDirectory($module)->withEntryPoints([$asset]);
    }
}

if (!function_exists('withTablePrefix')) {
    /**
     * @param string $table
     * @return string
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-29 下午2:55
     */
    function withTablePrefix(string $table): string
    {
        return DB::connection()->getTablePrefix() . $table;
    }
}

if (!function_exists('getGuardName')) {
    /**
     * @return string
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-29 下午3:02
     */
    function getGuardName(): string
    {
        $guardKeys = array_keys(config('modules.auth.guards', []));

        if (count($guardKeys)) {
            return $guardKeys[0];
        }

        return 'sanctum';
    }
}

if (!function_exists('getTableColumns')) {
    /**
     * @param string $table
     * @return array
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-29 下午3:02
     */
    function getTableColumns(string $table): array
    {
        $SQL = 'desc ' . withTablePrefix($table);

        $columns = [];

        foreach (DB::select($SQL) as $column) {
            $columns[] = $column->Field;
        }

        return $columns;
    }
}

if (!function_exists('getAuthUserModel')) {
    /**
     * @return mixed
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-29 下午3:03
     */
    function getAuthUserModel(): mixed
    {
        return config('modules.auth_model');
    }
}

if (!function_exists('isRequestFromAjax')) {
    /**
     * @return bool
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-29 下午3:03
     */
    function isRequestFromAjax(): bool
    {
        return Request::ajax();
    }
}
