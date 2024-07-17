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

namespace Nwidart\Modules\Commands\Actions;

use Nwidart\Modules\Commands\BaseCommand;

class DumpCommand extends BaseCommand
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dump-autoload the specified module or for all module.';
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:dump';

    public function executeAction($name): void
    {
        $module = $this->getModuleModel($name);

        $this->components->task("Generating for <fg=cyan;options=bold>{$module->getName()}</> Module", function () use ($module) {
            chdir($module->getPath());

            passthru('composer dump -o -n -q');
        });
    }

    public function getInfo(): ?string
    {
        return 'Generating optimized autoload modules';
    }
}
