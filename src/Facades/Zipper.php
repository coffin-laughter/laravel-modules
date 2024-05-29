<?php

declare(strict_types=1);

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
use Nwidart\Modules\Support\Zip\Zipper as Zip;

/**
 * @method static Zip make(string $pathToFile)
 * @method static Zip zip(string $pathToFile)
 * @method static Zip phar(string $pathToFile)
 *
 * @see Zipper
 * Class Module
 */
class Zipper extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return Zip::class;
    }
}
