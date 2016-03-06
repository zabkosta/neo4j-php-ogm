<?php

$loader = require_once __DIR__.'/../vendor/autoload.php';
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

use Demo\Entity\User;
use Demo\Entity\Movie;

$driver = \GraphAware\Neo4j\Client\ClientBuilder::create()
    ->addConnection('default', 'http://neo4j:error@localhost:7474')
    ->build();

$em = new \GraphAware\Neo4j\OGM\Manager($driver);

$movie = new Movie('Space Balls');
/** @var \Demo\Entity\User $user */
$user = $em->getRepository(User::class)->findOneBy('login', 'nigel');

$rating = $user->rateMovie($movie, 5);
$movie->addRating($rating);
$em->persist($user);
$em->persist($movie);
$em->flush();

//print_r($user);
