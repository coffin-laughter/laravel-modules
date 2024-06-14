<?php

declare(strict_types=1);
/**
 *  +-------------------------------------------------------------------------------------------
 *  | CoffinAdmin [ 花开不同赏，花落不同悲。欲问相思处，花开花落时。 ]
 *  +-------------------------------------------------------------------------------------------
 *  | This is not a free software, without any authorization is not allowed to use and spread.
 *  +-------------------------------------------------------------------------------------------
 *  | Copyright (c) 2006~2024 All rights reserved.
 *  +-------------------------------------------------------------------------------------------
 *  | @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
 *  +-------------------------------------------------------------------------------------------
 */
use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$year = date('Y');
$header = <<<HEADERCOMMENT
 +-------------------------------------------------------------------------------------------
 | Coffin [ 花开不同赏，花落不同悲。欲问相思处，花开花落时。 ]
 +-------------------------------------------------------------------------------------------
 | This is not a free software, without any authorization is not allowed to use and spread.
 +-------------------------------------------------------------------------------------------
 | Copyright (c) 2006~{$year} All rights reserved.
 +-------------------------------------------------------------------------------------------
 | @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
 +-------------------------------------------------------------------------------------------
HEADERCOMMENT;

$rules = [
    '@PSR12'         => true,
    'header_comment' => [
        'header'       => $header,
        'comment_type' => 'PHPDoc',
        'separate'     => 'none',
        'location'     => 'after_declare_strict',
    ],
    'binary_operator_spaces' => [
        'operators' => ['=>' => 'align_single_space'],
    ], //等号对齐、数字箭头符号对齐
    'blank_line_after_opening_tag'       => true,
    'compact_nullable_type_declaration'  => true,
    'declare_equal_normalize'            => true,
    'lowercase_cast'                     => true,
    'lowercase_static_reference'         => true,
    'new_with_parentheses'               => true,
    'no_blank_lines_after_class_opening' => true,
    'no_leading_import_slash'            => true,
    'ordered_class_elements'             => [
        'order' => [
            'use_trait',
            'constant_public',
            'constant_protected',
            'constant_private',
            'public',
            'protected',
            'private',
            'method_public',
            'method_protected',
            'method_private',
        ],
        'sort_algorithm' => 'alpha',
    ],
    'ordered_imports' => [
        'imports_order' => [
            'class',
            'function',
            'const',
        ],
        'sort_algorithm' => 'alpha',
    ],
    'return_type_declaration' => true,
    'short_scalar_cast'       => true,
    // 'single_blank_line_before_namespace' => true,
    'single_trait_insert_per_statement' => true,
    'ternary_operator_spaces'           => true,
    'unary_operator_spaces'             => true,
    'visibility_required'               => [
        'elements' => [
            'const',
            'method',
            'property',
        ],
    ],
    'align_multiline_comment'     => true,
    'no_trailing_whitespace'      => true,
    'echo_tag_syntax'             => true,
    'no_unused_imports'           => true, // 删除没用到的use
    'no_empty_statement'          => true, //多余的分号
    'no_whitespace_in_blank_line' => true, //删除空行中的空格
    'concat_space'                => ['spacing' => 'one'], // .拼接必须有空格分割
    'array_syntax'                => ['syntax' => 'short'],
    'single_quote'                => true, //简单字符串应该使用单引号代替双引号
    'blank_line_before_statement' => [
        'statements' => [
            'break',
            'continue',
            'declare',
            'return',
            'throw',
            'try',
        ],
    ], // 空行换行必须在任何已配置的语句之前
    'no_trailing_comma_in_singleline' => true, // 删除单行数组中的逗号
];

$finder = Finder::create()
    ->exclude(['tests', 'vendor', 'storage'])
    ->in([
        __DIR__ . '/config',
        __DIR__ . '/src',
    ])
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config())
    ->setFinder($finder)
    ->setRules($rules)
    ->setRiskyAllowed(true)
    ->setUsingCache(true);
