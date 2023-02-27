<?php

declare(strict_types = 1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

// https://symfony.com/doc/current/components/finder.html
$finder = Finder::create()
    ->in('src')
    ->in('config')
    ->name('*.php')
    ->name('_ide_helper');

$config = new Config();

return $config->setRules([
        '@PSR2' => true,
        '@PSR12' => true,
        'array_syntax' => [ 'syntax' => 'short' ],
        'binary_operator_spaces' => ['operators' => ['=' => 'single_space', '=>' => 'single_space']],
        'cast_spaces' => true,
        'combine_consecutive_unsets' => true,
        'concat_space' => [ 'spacing' => 'one' ],
        'linebreak_after_opening_tag' => true,
        'no_blank_lines_after_class_opening' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_extra_blank_lines' => true,
        'no_trailing_comma_in_singleline_array' => true,
        'no_whitespace_in_blank_line' => true,
        'no_spaces_around_offset' => true,
        'no_unused_imports' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'no_whitespace_before_comma_in_array' => true,
        'normalize_index_brace' => true,
        'phpdoc_indent' => true,
        'phpdoc_to_comment' => true,
        'phpdoc_trim' => true,
        'ternary_to_null_coalescing' => true,
        'trailing_comma_in_multiline' => true,
        'trim_array_spaces' => true,
        'no_break_comment' => false,
        'blank_line_before_statement' => false,
        'elseif' => false,
        'declare_equal_normalize' => ['space' =>'single'],
        'space_after_semicolon' => ['remove_in_empty_for_expressions' => true],
        'declare_strict_types' => true,
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setCacheFile(__DIR__ . '/.php-cs-fixer.cache');
