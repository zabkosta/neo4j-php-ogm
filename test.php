<?php

$loader = require_once __DIR__.'/vendor/autoload.php';

use GraphAware\Neo4j\OGM\Lazy\LazyRelationshipCollection;

$coll = new LazyRelationshipCollection();
print_r($coll->getValues());


