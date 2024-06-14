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

use Illuminate\Support\Str;
use Nwidart\Modules\Commands\BaseCommand;
use Symfony\Component\Console\Input\InputOption;

class PublishConfigurationCommand extends BaseCommand
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish a module\'s config files to the application';
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:publish-config';

    public function executeAction($name): void
    {
        $this->call('vendor:publish', [
            '--provider' => $this->getServiceProviderForModule($name),
            '--force'    => $this->option('force'),
            '--tag'      => ['config'],
        ]);
    }

    public function getInfo(): string|null
    {
        return 'Publishing module config files ...';
    }

    /**
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['--force', '-f', InputOption::VALUE_NONE, 'Force the publishing of config files'],
        ];
    }

    /**
     * @param string $module
     * @return string
     */
    private function getServiceProviderForModule($module): string
    {
        $namespace = $this->laravel['config']->get('modules.namespace');
        $studlyName = Str::studly($module);
        $provider = $this->laravel['config']->get('modules.paths.generator.provider.path');
        $provider = str_replace('/', '\\', $provider);

        return "$namespace\\$studlyName\\$provider\\{$studlyName}ServiceProvider";
    }
}
