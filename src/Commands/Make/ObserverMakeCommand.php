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

class ObserverMakeCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    /**
     * The name of argument name.
     *
     * @var string
     */
    protected $argumentName = 'name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new observer for the specified module.';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:make-observer';

    /**
     * Get default namespace.
     *
     * @return string
     */
    public function getDefaultNamespace(): string
    {
        return config('modules.paths.generator.observer.namespace')
            ?? ltrim(config('modules.paths.generator.observer.path', 'Observers'), config('modules.paths.app_folder', ''));
    }

    /**
     * Get model namespace.
     *
     * @return string
     */
    public function getModelNamespace(): string
    {
        $path = $this->laravel['modules']->config('paths.generator.model.path', 'Entities');

        $path = str_replace('/', '\\', $path);

        return $this->laravel['modules']->config('namespace') . '\\' . $this->laravel['modules']->findOrFail($this->getModuleName()) . '\\' . $path;
    }

    public function handle(): int
    {
        $this->components->info('Creating observer...');

        parent::handle();

        return 0;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The observer name will be created.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be created.'],
        ];
    }

    /**
     * @return mixed
     */
    protected function getDestinationFilePath()
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        $observerPath = GenerateConfigReader::read('observer');

        return $path . $observerPath->getPath() . '/' . $this->getFileName();
    }

    /**
     * @return mixed
     */
    protected function getTemplateContents()
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        return (new Stub('/observer.stub', [
                'NAMESPACE'       => $this->getClassNamespace($module),
                'NAME'            => $this->getModelName(),
                'MODEL_NAMESPACE' => $this->getModelNamespace(),
                'NAME_VARIABLE'   => $this->getModelVariable(),
            ]))->render();
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        return Str::studly($this->argument('name')) . 'Observer.php';
    }

    /**
     * @return mixed|string
     */
    private function getModelName()
    {
        return Str::studly($this->argument('name'));
    }

    /**
     *  @return mixed|string
     */
    private function getModelVariable(): string
    {
        return '$' . Str::lower($this->argument('name'));
    }
}
