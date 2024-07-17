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

namespace Nwidart\Modules\Tests\Facades;

use Nwidart\Modules\Facades\Module;
use Nwidart\Modules\Tests\BaseTestCase;

class ModuleFacadeTest extends BaseTestCase
{
    public function test_it_calls_macros_via_facade()
    {
        $modules = Module::macro('testMacro', function () {
            return 'a value';
        });

        $this->assertEquals('a value', Module::testMacro());
    }

    public function test_it_creates_macros_via_facade()
    {
        $modules = Module::macro('testMacro', function () {
            return true;
        });

        $this->assertTrue(Module::hasMacro('testMacro'));
    }
    public function test_it_resolves_the_module_facade()
    {
        $modules = Module::all();

        $this->assertTrue(is_array($modules));
    }
}
