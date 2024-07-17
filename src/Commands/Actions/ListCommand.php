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

use Illuminate\Console\Command;
use Nwidart\Modules\Module;
use Symfony\Component\Console\Input\InputOption;

class ListCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show list of all modules.';
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:list';

    public function getModules()
    {
        switch ($this->option('only')) {
            case 'enabled':
                return $this->laravel['modules']->getByStatus(1);

                break;

            case 'disabled':
                return $this->laravel['modules']->getByStatus(0);

                break;

            case 'priority':
                return $this->laravel['modules']->getPriority($this->option('direction'));

                break;

            default:
                return $this->laravel['modules']->all();

                break;
        }
    }

    /**
     * Get table rows.
     *
     * @return array
     */
    public function getRows()
    {
        $rows = [];

        /** @var Module $module */
        foreach ($this->getModules() as $module) {
            $rows[] = [
                $module->getName(),
                $module->isEnabled() ? '<fg=green>Enabled</>' : '<fg=red>Disabled</>',
                $module->get('priority'),
                $module->getPath(),
            ];
        }

        return $rows;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->components->twoColumnDetail('<fg=gray>Status / Name</>', '<fg=gray>Path / priority</>');
        collect($this->getRows())->each(function ($row) {

            $this->components->twoColumnDetail("[{$row[1]}] {$row[0]}", "{$row[3]} [{$row[2]}]");
        });

        return 0;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['only', 'o', InputOption::VALUE_OPTIONAL, 'Types of modules will be displayed.', null],
            ['direction', 'd', InputOption::VALUE_OPTIONAL, 'The direction of ordering.', 'asc'],
        ];
    }
}
