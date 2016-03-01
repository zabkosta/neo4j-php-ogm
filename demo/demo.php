<?php

$loader = require_once __DIR__.'/../vendor/autoload.php';
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

$driver = \GraphAware\Neo4j\Client\ClientBuilder::create()
    ->addConnection('default', 'http://neo4j:error@localhost:7474')
    ->build();

$em = new \GraphAware\Neo4j\OGM\Manager($driver);

use Demo\Entity\User;
use Demo\Entity\Company;

$company = new Company("Acme");
$alessandro = new User("ale");
$chris = new User("chris");
$company->addMember($alessandro);
$company->addMember($chris);
$chris->setCompany($company);
$alessandro->setCompany($company);
$alessandro->addFriend($chris);

print_r($em->getClassMetadataFor($company));
