<?php

$loader = require_once __DIR__.'/../vendor/autoload.php';
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

use Demo\Entity\User;

$driver = \GraphAware\Neo4j\Client\ClientBuilder::create()
    ->addConnection('default', 'http://neo4j:error@localhost:7474')
    ->build();

$em = new \GraphAware\Neo4j\OGM\Manager($driver);

$repo = $em->getRepository(User::class);
$ale = $repo->findOneBy('login', 'alenegro81');
$ale->setAge(99);

$em->persist($ale);
$em->flush();

$users = $repo->findAll();

foreach ($users as $user) {
    $user->setAge(120);
    $em->persist($user);
}
$em->flush();

//print_r($user);