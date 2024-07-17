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

class EnableCommand extends BaseCommand
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable the specified module.';
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:enable';

    public function executeAction($name): void
    {
        $module = $this->getModuleModel($name);

        $status = $module->isDisabled()
            ? '<fg=red;options=bold>Disabled</>'
            : '<fg=green;options=bold>Enabled</>';

        $this->components->task("Enabling <fg=cyan;options=bold>{$module->getName()}</> Module, old status: $status", function () use ($module) {
            $module->enable();
        });
    }

    public function getInfo(): ?string
    {
        return 'Disabling module ...';
    }
}
