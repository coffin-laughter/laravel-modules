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

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Str;
use Nwidart\Modules\Support\Config\GenerateConfigReader;
use Nwidart\Modules\Support\Stub;
use Nwidart\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;

class ViewMakeCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    protected $argumentName = 'name';
    protected $description = 'Create a new view for the specified module.';
    protected $name = 'module:make-view';

    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the view.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
            ['dir', InputArgument::OPTIONAL, 'The name of module\'s directory.'],
        ];
    }

    protected function getDestinationFilePath(): string
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());
        $factoryPath = GenerateConfigReader::read('views');

        return $path . $factoryPath->getPath() . '/' . $this->getFileName();
    }

    protected function getTemplateContents(): string
    {
        return (new Stub('/view.stub', ['QUOTE' => Inspiring::quotes()->random()]))->render();
    }

    /**
     * @return string
     */
    private function getFileName(): string
    {
        return Str::lower($this->argument('name')) . '.blade.php';
    }
}
