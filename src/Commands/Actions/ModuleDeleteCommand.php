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
use Nwidart\Modules\Contracts\ConfirmableCommand;

class ModuleDeleteCommand extends BaseCommand implements ConfirmableCommand
{
    protected $description = 'Delete a module from the application';
    protected $name = 'module:delete';

    public function executeAction($name): void
    {
        $module = $this->getModuleModel($name);
        $this->components->task("Deleting <fg=cyan;options=bold>{$module->getName()}</> Module", function () use ($module) {
            $module->delete();
        });
    }

    public function getConfirmableLabel(): string
    {
        return 'Warning: Do you want to remove the module?';
    }

    public function getInfo(): ?string
    {
        return 'deleting module ...';
    }
}
