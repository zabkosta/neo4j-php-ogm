<?php

require_once __DIR__.'/../../../vendor/autoload.php';

use GraphAware\Neo4j\OGM\Manager;
use Movies\Person;

$manager = Manager::create('http://localhost:7676');

$personRepository = $manager->getRepository(Person::class);
$tomHanks = $personRepository->findOneBy('name', 'Tom Hanks');

$actor = new Person('Kevin Ross', 1976);
$manager->persist($actor);
$manager->flush();