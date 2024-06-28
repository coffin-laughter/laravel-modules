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

namespace Nwidart\Modules\Commands\Make;

use Illuminate\Support\Str;
use Nwidart\Modules\Support\Config\GenerateConfigReader;
use Nwidart\Modules\Support\Stub;
use Nwidart\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class EventProviderMakeCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    protected $argumentName = 'module';
    protected $description = 'Create a new event service provider class for the specified module.';
    protected $name = 'module:make-event-provider';

    public function getDefaultNamespace(): string
    {
        return config('modules.paths.generator.provider.namespace')
            ?? ltrim(config('modules.paths.generator.provider.path', 'Providers'), config('modules.paths.app_folder', ''));
    }

    public function getDestinationFilePath(): string
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        $filePath = GenerateConfigReader::read('provider')->getPath();

        return $path . $filePath . '/' . $this->getEventServiceProviderName() . '.php';
    }

    protected function getArguments(): array
    {
        return [
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
            ['dir', InputArgument::OPTIONAL, 'The name of module\'s directory.'],
        ];
    }

    protected function getEventServiceProviderName(): array|string
    {
        return Str::studly('EventServiceProvider');
    }

    /**
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'su.'],
        ];
    }

    protected function getStubName(): string
    {
        return '/event-provider.stub';
    }

    protected function getTemplateContents(): string
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        return (new Stub($this->getStubName(), [
            'NAMESPACE' => $this->getClassNamespace($module),
            'CLASS'     => $this->getClassNameWithoutNamespace(),
        ]))->render();
    }

    private function getClassNameWithoutNamespace(): array|string
    {
        return class_basename($this->getEventServiceProviderName());
    }
}
