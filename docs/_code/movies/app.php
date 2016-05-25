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

$tomHanks->setBorn(1990);
$manager->flush();

$manager->clear();

$tomHanks = $manager->getRepository(Person::class)->findOneBy('name', 'Tom Hanks');
echo sprintf('Tom Hanks played in %d movies', count($tomHanks->getMovies())) . PHP_EOL;

foreach ($tomHanks->getMovies() as $movie) {
    echo $movie->getTitle() . PHP_EOL;

    echo count($movie->getActors()) . PHP_EOL;
}