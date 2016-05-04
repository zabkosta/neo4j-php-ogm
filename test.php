<?php

$loader = require_once __DIR__.'/vendor/autoload.php';

use GraphAware\Neo4j\Client\ClientBuilder;
use GraphAware\Neo4j\OGM\Manager;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\User;
use Symfony\Component\Finder\Finder;

$finder = new Finder();
$finder->files()->name('*.php')->in(__DIR__.'/src/Annotations');

foreach ($finder as $file) {
    require_once $file->getRealpath();
}

$client = ClientBuilder::create()
    ->addConnection('bolt', 'http://localhost:7474')
    ->build();

$em = new Manager($client);
$repository = $em->getRepository(User::class);
$s = microtime(true);
$users = $repository->findAll();
echo count($users) . PHP_EOL;
echo microtime(true) - $s;
