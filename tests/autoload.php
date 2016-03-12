<?php
/*
 * This file bootstraps the test environment.
 */
error_reporting(E_ALL | E_STRICT);

$loader = require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Finder\Finder;

$finder = new Finder();
$finder->files()->name('*.php')->in(__DIR__.'/../src/Annotations');

foreach ($finder as $file) {
    require_once $file->getRealpath();
}