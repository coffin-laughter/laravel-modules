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

namespace Nwidart\Modules\Commands\Database;

use Nwidart\Modules\Commands\BaseCommand;
use Nwidart\Modules\Migrations\Migrator;
use Nwidart\Modules\Traits\MigrationLoaderTrait;
use Symfony\Component\Console\Input\InputOption;

class MigrateResetCommand extends BaseCommand
{
    use MigrationLoaderTrait;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the modules migrations.';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:migrate-reset';

    public function executeAction($name): void
    {
        $module = $this->getModuleModel($name);

        $migrator = new Migrator($module, $this->getLaravel());

        $database = $this->option('database');

        if (!empty($database)) {
            $migrator->setDatabase($database);
        }

        $migrated = $migrator->reset();

        if (count($migrated)) {
            foreach ($migrated as $migration) {
                $this->line("Rollback: <info>{$migration}</info>");
            }

            return;
        }

        $this->components->warn("Nothing to rollback on module <fg=cyan;options=bold>{$module->getName()}</>");
    }

    public function getInfo(): ?string
    {
        return null;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['direction', 'd', InputOption::VALUE_OPTIONAL, 'The direction of ordering.', 'desc'],
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'],
        ];
    }
}
