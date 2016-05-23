<?php

$loader = require_once __DIR__.'/vendor/autoload.php';

$personA = new \GraphAware\Neo4j\OGM\Tests\Integration\Model\Person('ikwattro');
$personB = new \GraphAware\Neo4j\OGM\Tests\Integration\Model\Person('jexp');
$ca = clone($personA);
$arr = [
    'a' => spl_object_hash($personA),
    'b' => spl_object_hash($personB),
    'c' => spl_object_hash($ca)
];
$arr2 = [
    'a' => $personA
];

print_r($arr);

unset($personA);

print_r($arr2);
print_r($arr);

