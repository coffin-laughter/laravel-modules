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

namespace Nwidart\Modules\Tests\Support\Config;

use Nwidart\Modules\Support\Config\GenerateConfigReader;
use Nwidart\Modules\Support\Config\GeneratorPath;
use Nwidart\Modules\Tests\BaseTestCase;

final class GenerateConfigReaderTest extends BaseTestCase
{
    public function test_it_can_guess_namespace_from_path()
    {
        $this->app['config']->set('modules.paths.generator.provider', ['path' => 'Base/Providers', 'generate' => true]);

        $config = GenerateConfigReader::read('provider');

        $this->assertEquals('Base/Providers', $config->getPath());
        $this->assertEquals('Base\Providers', $config->getNamespace());
    }
    public function test_it_can_read_a_configuration_value_with_new_format()
    {
        $seedConfig = GenerateConfigReader::read('seeder');

        $this->assertInstanceOf(GeneratorPath::class, $seedConfig);
        $this->assertEquals('database/seeders', $seedConfig->getPath());
        $this->assertTrue($seedConfig->generate());
    }

    public function test_it_can_read_a_configuration_value_with_new_format_set_to_false()
    {
        $this->app['config']->set('modules.paths.generator.seeder', ['path' => 'Database/Seeders', 'generate' => false]);

        $seedConfig = GenerateConfigReader::read('seeder');

        $this->assertInstanceOf(GeneratorPath::class, $seedConfig);
        $this->assertEquals('Database/Seeders', $seedConfig->getPath());
        $this->assertFalse($seedConfig->generate());
    }

    public function test_it_can_read_a_configuration_value_with_old_format()
    {
        $this->app['config']->set('modules.paths.generator.seeder', 'Database/Seeders');

        $seedConfig = GenerateConfigReader::read('seeder');

        $this->assertInstanceOf(GeneratorPath::class, $seedConfig);
        $this->assertEquals('Database/Seeders', $seedConfig->getPath());
        $this->assertTrue($seedConfig->generate());
    }

    public function test_it_can_read_a_configuration_value_with_old_format_set_to_false()
    {
        $this->app['config']->set('modules.paths.generator.seeder', false);

        $seedConfig = GenerateConfigReader::read('seeder');

        $this->assertInstanceOf(GeneratorPath::class, $seedConfig);
        $this->assertFalse($seedConfig->getPath());
        $this->assertFalse($seedConfig->generate());
    }
}
