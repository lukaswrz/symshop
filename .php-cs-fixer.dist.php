<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'ordered_imports' => true,
        'no_unused_imports' => true,
        'declare_strict_types' => true
    ])
    ->setFinder($finder)
;
