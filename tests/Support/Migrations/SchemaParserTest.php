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

namespace Nwidart\Modules\Tests\Support\Migrations;

use Nwidart\Modules\Support\Migrations\SchemaParser;
use PHPUnit\Framework\TestCase;

class SchemaParserTest extends TestCase
{
    public function test_it_generates_migration_method_calls()
    {
        $parser = new SchemaParser('username:string, password:integer');

        $expected = <<<TEXT
\t\t\t\$table->string('username');
\t\t\t\$table->integer('password');\n
TEXT;

        self::assertEquals($expected, $parser->render());
    }

    public function test_it_generates_migration_methods_for_down_method()
    {
        $parser = new SchemaParser('username:string, password:integer');

        $expected = <<<TEXT
\t\t\t\$table->dropColumn('username');
\t\t\t\$table->dropColumn('password');\n
TEXT;

        self::assertEquals($expected, $parser->down());
    }

    public function test_it_generates_migration_methods_for_up_method()
    {
        $parser = new SchemaParser('username:string, password:integer');

        $expected = <<<TEXT
\t\t\t\$table->string('username');
\t\t\t\$table->integer('password');\n
TEXT;

        self::assertEquals($expected, $parser->up());
    }
}
