<?php

$loader = require_once __DIR__.'/../vendor/autoload.php';
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

use Demo\Entity\User;

$driver = \GraphAware\Neo4j\Client\ClientBuilder::create()
    ->addConnection('default', 'http://neo4j:error@localhost:7474')
    ->build();

$em = new \GraphAware\Neo4j\OGM\Manager($driver);

/*
$nigel = new User('nigel');
$nigel->setAge(33);
$company = new \Demo\Entity\Company("Neo Technology");
$nigel->setCompany($company);
$company->addMember($nigel);

$ale = new User('jake');
$ale->setAge(34);
$ale->setCompany($company);

$mi = new User('michael');
$mi->setAge(30);
$mi->setCompany($company);

$company->addMember($ale);
$company->addMember($mi);

$nigel->addFriend($ale);
$nigel->addFriend($mi);

$em->persist($company);
$em->flush();
*/
$s = microtime(true);
$users = $em->getRepository(User::class)->findAll();
foreach ($users as $user) {
    $user->setAge(35);
    $em->persist($user);
}
$em->flush();

$e = microtime(true);
echo $e - $s .PHP_EOL;
//print_r($users);