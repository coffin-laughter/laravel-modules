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

namespace Nwidart\Modules\Contracts;

use Nwidart\Modules\Exceptions\ModuleNotFoundException;
use Nwidart\Modules\Module;

interface RepositoryInterface
{
    /**
     * Get all modules.
     *
     * @return mixed
     */
    public function all();

    /**
     * Get list of disabled modules.
     *
     * @return mixed
     */
    public function allDisabled();

    /**
     * Get list of enabled modules.
     *
     * @return mixed
     */
    public function allEnabled();

    /**
     * Get asset path for a specific module.
     *
     * @param string $module
     * @return string
     */
    public function assetPath(string $module): string;

    /**
     * Boot the modules.
     */
    public function boot(): void;

    /**
     * Get a specific config data from a configuration file.
     * @param string $key
     *
     * @param string|null $default
     * @return mixed
     */
    public function config(string $key, $default = null);

    /**
     * Get count from all modules.
     *
     * @return int
     */
    public function count();

    /**
     * Delete a specific module.
     * @param string $module
     * @return bool
     * @throws \Nwidart\Modules\Exceptions\ModuleNotFoundException
     */
    public function delete(string $module): bool;

    /**
     * Find a specific module.
     *
     * @param $name
     * @return Module|null
     */
    public function find(string $name);

    /**
     * Find a specific module. If there return that, otherwise throw exception.
     *
     * @param $name
     *
     * @return mixed
     */
    public function findOrFail(string $name);

    /**
     * Get modules by the given status.
     *
     * @param int $status
     *
     * @return mixed
     */
    public function getByStatus($status);

    /**
     * Get cached modules.
     *
     * @return array
     */
    public function getCached();

    /**
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getFiles();

    public function getModulePath($moduleName);

    /**
     * Get all ordered modules.
     * @param string $direction
     * @return mixed
     */
    public function getOrdered($direction = 'asc');

    /**
     * Get a module path.
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * Get scanned paths.
     *
     * @return array
     */
    public function getScanPaths();

    /**
     * Determine whether the given module is not activated.
     * @param string $name
     * @return bool
     * @throws ModuleNotFoundException
     */
    public function isDisabled(string $name): bool;

    /**
     * Determine whether the given module is activated.
     * @param string $name
     * @return bool
     * @throws ModuleNotFoundException
     */
    public function isEnabled(string $name): bool;

    /**
     * Register the modules.
     */
    public function register(): void;

    /**
     * Scan & get all available modules.
     *
     * @return array
     */
    public function scan();

    /**
     * Get modules as modules collection instance.
     *
     * @return \Nwidart\Modules\Collection
     */
    public function toCollection();
}
