<?php

$loader = require_once __DIR__.'/vendor/autoload.php';

use GraphAware\Neo4j\OGM\EntityManager;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\User;

$em = EntityManager::create("http://localhost:7676", __DIR__.'/_var');
$em->getDatabaseDriver()->run("MATCH (n) DETACH DELETE n");

$personA = new User('ikwattro');

for ($i = 1; $i < 100; ++$i) {
   $friend = new User(sprintf('friend%d', $i));
    $personA->getFriends()->add($friend);
}
$em->persist($personA);
$em->flush();


