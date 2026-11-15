<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

return (new Config())
    ->setRiskyAllowed(false)
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_whitespace_before_comma_in_array' => true,
        'no_whitespace_in_empty_array' => true,
        'trim_array_spaces' => true,
        'whitespace_after_comma_in_array' => ['ensure_single_space' => true],
        'no_trailing_comma_in_singleline' => true,
        'class_reference_name_casing' => true,
        'cast_spaces' => ['space'=>'single'],
        'multiline_comment_opening_closing' => true,
        'no_superfluous_elseif' => true,
        'no_useless_else' => true,
        'fully_qualified_strict_types' => ['import_symbols' => true],
        'no_unneeded_import_alias' => true,
        'no_unused_imports' => true,
        'ordered_imports'=> ['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'alpha'],
        'combine_consecutive_issets'=>true,
        'combine_consecutive_unsets' => true,
        'no_useless_return' => true,
        'array_indentation' => true
    ]
    )
    // 💡 by default, Fixer looks for `*.php` files excluding `./vendor/` - here, you can groom this config
    ->setFinder(
        (new Finder())
            // 💡 root folder to check
            ->in(__DIR__)
            
    )
;
