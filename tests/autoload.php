<?php
/*
 * This file bootstraps the test environment.
 */
error_reporting(E_ALL | E_STRICT);
$basedir = __DIR__.'/../';
$proxyDir = $basedir . DIRECTORY_SEPARATOR . '_var';
putenv("basedir=$basedir");
putenv("proxydir=$proxyDir");
$loader = require_once __DIR__.'/../vendor/autoload.php';