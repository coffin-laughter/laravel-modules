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

namespace Nwidart\Modules\Commands\Publish;

use Nwidart\Modules\Commands\BaseCommand;
use Nwidart\Modules\Migrations\Migrator;
use Nwidart\Modules\Publishing\MigrationPublisher;

class PublishMigrationCommand extends BaseCommand
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Publish a module's migrations to the application";
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:publish-migration';

    public function executeAction($name): void
    {
        $module = $this->getModuleModel($name);

        $this->components->task("Publishing Migration <fg=cyan;options=bold>{$module->getName()}</> Module", function () use ($module) {
            with(new MigrationPublisher(new Migrator($module, $this->getLaravel())))
                ->setRepository($this->laravel['modules'])
                ->setConsole($this)
                ->publish();
        });
    }

    public function getInfo(): ?string
    {
        return 'Publishing module migrations ...';
    }
}
