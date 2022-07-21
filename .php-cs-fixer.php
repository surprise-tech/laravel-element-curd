<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->exclude('database/migrations')
    ->in(__DIR__);

$config = new PhpCsFixer\Config();

return $config->setRules([
    '@Symfony' => true,
])
    ->setFinder($finder);
