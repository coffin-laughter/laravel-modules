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

namespace Nwidart\Modules\Tests\Traits;

use Nwidart\Modules\Tests\BaseTestCase;

class PathNamespaceTest extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->class = new UsePathNamespaceTrait();
    }

    public function test_app_path()
    {
        $configPath = config('modules.paths.app_folder');
        $configPath = rtrim($configPath, '/');

        $this->assertSame($configPath, $this->class->app_path());
        $this->assertSame($configPath, $this->class->app_path(null));
        $this->assertSame('app/blog/services', $this->class->app_path('blog/services'));
    }

    public function test_clean_path()
    {
        $this->assertSame('blog/services', $this->class->clean_path('blog/services'));
        $this->assertSame('', $this->class->clean_path(''));
    }

    public function test_module_namespace()
    {
        $this->assertSame('Modules\Blog/services', $this->class->module_namespace('blog/services'));
    }

    public function test_path_namespace()
    {
        $this->assertSame('Blog\Services', $this->class->path_namespace('/blog/services'));
    }

    public function test_studly_namespace()
    {
        $this->assertSame('/blog/services', $this->class->studly_namespace('/blog/services'));
    }

    public function test_studly_path()
    {
        $this->assertSame('Blog/Services', $this->class->studly_path('/blog/services'));
    }
}
