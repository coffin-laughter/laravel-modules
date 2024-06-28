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

class TestMakeCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    protected $argumentName = 'name';
    protected $description = 'Create a new test class for the specified module.';
    protected $name = 'module:make-test';

    public function getDefaultNamespace(): string
    {
        if ($this->option('feature')) {
            return config('modules.paths.generator.test-feature.namespace')
                ?? config('modules.paths.generator.test-feature.path', 'tests/Feature');
        }

        return config('modules.paths.generator.test-unit.namespace')
            ?? config('modules.paths.generator.test-unit.path', 'tests/Unit');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the form request class.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
            ['dir', InputArgument::OPTIONAL, 'The name of module\'s directory.'],
        ];
    }

    /**
     * @return mixed
     */
    protected function getDestinationFilePath()
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        if ($this->option('feature')) {
            $testPath = GenerateConfigReader::read('test-feature');
        } else {
            $testPath = GenerateConfigReader::read('test-unit');
        }

        return $path . $testPath->getPath() . '/' . $this->getFileName() . '.php';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['feature', null, InputOption::VALUE_NONE, 'Create a feature test.'],
        ];
    }

    /**
     * @return mixed
     */
    protected function getTemplateContents()
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());
        $stub = '/unit-test.stub';

        if ($this->option('feature')) {
            $stub = '/feature-test.stub';
        }

        return (new Stub($stub, [
            'NAMESPACE' => $this->getClassNamespace($module),
            'CLASS'     => $this->getClass(),
        ]))->render();
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        return Str::studly($this->argument('name'));
    }
}
