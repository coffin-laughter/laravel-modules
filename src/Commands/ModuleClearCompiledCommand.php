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

namespace Nwidart\Modules\Commands;

use Illuminate\Console\Command;
use Nwidart\Modules\ModuleManifest;

class ModuleClearCompiledCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the module compiled class file';
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'module:clear-compiled';

    public function handle(ModuleManifest $manifest): void
    {
        if (is_file($manifest->manifestPath)) {
            @unlink($manifest->manifestPath);
        }

        $this->components->info('Compiled module files removed successfully.');
    }
}
