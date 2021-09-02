<?php
declare(strict_types=1);
$finder = (new PhpCsFixer\Finder())
    ->exclude('vendor')
    ->in(['src', 'test']);

return (new PhpCsFixer\Config())
    ->setUsingCache(false)
    ->setRiskyAllowed(true)
    ->setRules([
                   'declare_strict_types' => true,
                   '@PSR2' => true,
                   '@PSR12' => true,
                   '@PHP80Migration' => true,
               ])
    ->setFinder($finder)
;