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

namespace Nwidart\Modules\Generators;

use Illuminate\Filesystem\Filesystem;
use Nwidart\Modules\Exceptions\FileAlreadyExistException;

class FileGenerator extends Generator
{
    /**
     * The contens will be used.
     *
     * @var string
     */
    protected $contents;

    /**
     * The laravel filesystem or null.
     *
     * @var \Illuminate\Filesystem\Filesystem|null
     */
    protected $filesystem;
    /**
     * The path wil be used.
     *
     * @var string
     */
    protected $path;
    /**
     * @var bool
     */
    private $overwriteFile;

    /**
     * The constructor.
     *
     * @param      $path
     * @param      $contents
     * @param null $filesystem
     */
    public function __construct($path, $contents, $filesystem = null)
    {
        $this->path = $path;
        $this->contents = $contents;
        $this->filesystem = $filesystem ?: new Filesystem();
    }

    /**
     * Generate the file.
     */
    public function generate()
    {
        $path = $this->getPath();
        if (!$this->filesystem->exists($path)) {
            return $this->filesystem->put($path, $this->getContents());
        }
        if ($this->overwriteFile === true) {
            return $this->filesystem->put($path, $this->getContents());
        }

        throw new FileAlreadyExistException('File already exists!');
    }

    /**
     * Get contents.
     *
     * @return mixed
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Get filesystem.
     *
     * @return mixed
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * Get path.
     *
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set contents.
     *
     * @param mixed $contents
     *
     * @return $this
     */
    public function setContents($contents)
    {
        $this->contents = $contents;

        return $this;
    }

    /**
     * Set filesystem.
     *
     * @param Filesystem $filesystem
     *
     * @return $this
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;

        return $this;
    }

    /**
     * Set path.
     *
     * @param mixed $path
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    public function withFileOverwrite(bool $overwrite): FileGenerator
    {
        $this->overwriteFile = $overwrite;

        return $this;
    }
}
