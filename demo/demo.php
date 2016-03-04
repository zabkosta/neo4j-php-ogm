<?php

$loader = require_once __DIR__.'/../vendor/autoload.php';
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

use Demo\Entity\User;

$driver = \GraphAware\Neo4j\Client\ClientBuilder::create()
    ->addConnection('default', 'http://neo4j:error@localhost:7474')
    ->build();

$em = new \GraphAware\Neo4j\OGM\Manager($driver);


$user = new User('john');
$user->setAge(33);
$company = new \Demo\Entity\Company("Acme");
$user->setCompany($company);
$company->addMember($user);

$ale = new User('alenegro81');
$ale->setAge(34);
$ale->setCompany($company);

$mi = new User('bachmanm');
$mi->setAge(30);
$mi->setCompany($company);

$company->addMember($ale);
$company->addMember($mi);

$em->persist($company);
$em->flush();

//print_r($user);