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

namespace Nwidart\Modules\Traits;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

trait ModuleCommandTrait
{
    public function getModuleName(): string
    {
        $module = $this->argument('module') ?: app('modules')->getUsedNow();

        $dir = $this->argument('dir');

        if ($dir) {
            $dir = Str::ucfirst(Str::camel($dir));
            Config::set('modules.paths.modules', $dir);
            Config::set('modules.namespace', $dir);
        }

        $module = app('modules')->findOrFail($module);

        return $module->getStudlyName();
    }
}
