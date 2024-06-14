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

namespace Nwidart\Modules\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array all()
 * @method static array getCached()
 * @method static array scan()
 * @method static \Nwidart\Modules\Collection toCollection()
 * @method static array getScanPaths()
 * @method static array allEnabled()
 * @method static array allDisabled()
 * @method static int count()
 * @method static array getOrdered($direction = 'asc')
 * @method static array getByStatus($status)
 * @method static \Nwidart\Modules\Module find(string $name)
 * @method static \Nwidart\Modules\Module findOrFail(string $name)
 * @method static string getModulePath($moduleName)
 * @method static \Illuminate\Filesystem\Filesystem getFiles()
 * @method static mixed config(string $key, $default = NULL)
 * @method static string getPath()
 * @method static void boot()
 * @method static void register(): void
 * @method static string assetPath(string $module)
 * @method static bool delete(string $module)
 * @method static bool isEnabled(string $name)
 * @method static bool isDisabled(string $name)
 */
class Module extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'modules';
    }
}
