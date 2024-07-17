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

namespace Nwidart\Modules\Tests;

use Nwidart\Modules\Collection;
use Nwidart\Modules\Laravel\Module;

class CollectionTest extends BaseTestCase
{
    public function test_getItemsReturnsTheCollectionItems()
    {
        $modules = [
            new Module($this->app, 'module-one', __DIR__ . '/stubs/valid/Recipe'),
            new Module($this->app, 'module-two', __DIR__ . '/stubs/valid/Requirement'),
        ];
        $collection = new Collection($modules);
        $items = $collection->getItems();

        $this->assertCount(2, $items);
        $this->assertInstanceOf(Module::class, $items[0]);
    }
    public function test_toArraySetsPathAttribute()
    {
        $moduleOnePath = __DIR__ . '/stubs/valid/Recipe';
        $moduleTwoPath = __DIR__ . '/stubs/valid/Requirement';
        $modules = [
            new Module($this->app, 'module-one', $moduleOnePath),
            new Module($this->app, 'module-two', $moduleTwoPath),
        ];
        $collection = new Collection($modules);
        $collectionArray = $collection->toArray();

        $this->assertArrayHasKey('path', $collectionArray[0]);
        $this->assertEquals($moduleOnePath, $collectionArray[0]['path']);
        $this->assertArrayHasKey('path', $collectionArray[1]);
        $this->assertEquals($moduleTwoPath, $collectionArray[1]['path']);
    }
}
