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

class ServiceMakeCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    protected $argumentName = 'name';

    protected $description = 'Create a new service class for the specified module.';

    protected $name = 'module:make-service';

    public function getDefaultNamespace(): string
    {
        return config('modules.paths.generator.services.namespace', 'Services');
    }

    public function getDestinationFilePath(): string
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        $filePath = GenerateConfigReader::read('services')->getPath() ?? config('modules.paths.app_folder') . 'Services';

        return $path . $filePath . '/' . $this->getServiceName() . '.php';
    }

    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the service class.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
            ['dir', InputArgument::OPTIONAL, 'The name of module\'s directory.'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['invokable', 'i', InputOption::VALUE_NONE, 'Generate an invokable service class', null],
            ['force', 'f', InputOption::VALUE_NONE, 'su.'],
        ];
    }

    protected function getServiceName(): array|string
    {
        return Str::studly($this->argument('name'));
    }

    protected function getStubName(): string
    {
        return $this->option('invokable') === true ? '/service-invoke.stub' : '/service.stub';
    }

    protected function getTemplateContents(): string
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        return (new Stub($this->getStubName(), [
            'CLASS_NAMESPACE' => $this->getClassNamespace($module),
            'CLASS'           => $this->getClassNameWithoutNamespace(),
        ]))->render();
    }

    private function getClassNameWithoutNamespace(): array|string
    {
        return class_basename($this->getServiceName());
    }
}
