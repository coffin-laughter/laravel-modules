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

namespace Nwidart\Modules\Publishing;

use Illuminate\Console\Command;
use Nwidart\Modules\Contracts\PublisherInterface;
use Nwidart\Modules\Contracts\RepositoryInterface;
use Nwidart\Modules\Module;

abstract class Publisher implements PublisherInterface
{
    /**
     * The laravel console instance.
     *
     * @var \Illuminate\Console\Command
     */
    protected $console;

    /**
     * The error message will displayed at console.
     *
     * @var string
     */
    protected $error = '';
    /**
     * The name of module will used.
     *
     * @var string
     */
    protected $module;

    /**
     * The modules repository instance.
     *
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * Determine whether the result message will shown in the console.
     *
     * @var bool
     */
    protected $showMessage = true;

    /**
     * The success message will displayed at console.
     *
     * @var string
     */
    protected $success;

    /**
     * The constructor.
     */
    public function __construct(Module $module)
    {
        $this->module = $module;
    }

    /**
     * Get console instance.
     *
     * @return \Illuminate\Console\Command
     */
    public function getConsole()
    {
        return $this->console;
    }

    /**
     * Get destination path.
     *
     * @return string
     */
    abstract public function getDestinationPath();

    /**
     * Get laravel filesystem instance.
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getFilesystem()
    {
        return $this->repository->getFiles();
    }

    /**
     * Get module instance.
     *
     * @return \Nwidart\Modules\Module
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Get modules repository instance.
     *
     * @return RepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Get source path.
     *
     * @return string
     */
    abstract public function getSourcePath();

    /**
     * Hide the result message.
     *
     * @return self
     */
    public function hideMessage()
    {
        $this->showMessage = false;

        return $this;
    }

    /**
     * Publish something.
     */
    public function publish()
    {
        if (!$this->console instanceof Command) {
            $message = "The 'console' property must instance of \\Illuminate\\Console\\Command.";

            throw new \RuntimeException($message);
        }

        if (!$this->getFilesystem()->isDirectory($sourcePath = $this->getSourcePath())) {
            return;
        }

        if (!$this->getFilesystem()->isDirectory($destinationPath = $this->getDestinationPath())) {
            $this->getFilesystem()->makeDirectory($destinationPath, 0775, true);
        }

        if ($this->getFilesystem()->copyDirectory($sourcePath, $destinationPath)) {
            if ($this->showMessage === true) {
                $this->console->components->task($this->module->getStudlyName(), fn () => true);
            }
        } else {
            $this->console->components->task($this->module->getStudlyName(), fn () => false);
            $this->console->components->error($this->error);
        }
    }

    /**
     * Set console instance.
     *
     *
     * @return $this
     */
    public function setConsole(Command $console)
    {
        $this->console = $console;

        return $this;
    }

    /**
     * Set modules repository instance.
     *
     * @return $this
     */
    public function setRepository(RepositoryInterface $repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * Show the result message.
     *
     * @return self
     */
    public function showMessage()
    {
        $this->showMessage = true;

        return $this;
    }
}
