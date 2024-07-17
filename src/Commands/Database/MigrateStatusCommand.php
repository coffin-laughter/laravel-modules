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
use Symfony\Component\Console\Input\InputOption;

class MigrateStatusCommand extends BaseCommand
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Status for all module migrations';

    /**
     * @var \Nwidart\Modules\Contracts\RepositoryInterface
     */
    protected $module;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:migrate-status';

    public function executeAction($name): void
    {
        $module = $this->getModuleModel($name);

        $path = str_replace(base_path(), '', (new Migrator($module, $this->getLaravel()))->getPath());

        $this->call('migrate:status', [
            '--path'     => $path,
            '--database' => $this->option('database'),
        ]);
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
            ['direction', 'd', InputOption::VALUE_OPTIONAL, 'The direction of ordering.', 'asc'],
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
        ];
    }
}
