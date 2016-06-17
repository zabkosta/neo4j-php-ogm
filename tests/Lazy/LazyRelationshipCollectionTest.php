<?php

namespace GraphAware\Neo4j\OGM\Tests\Lazy;

use GraphAware\Neo4j\OGM\Lazy\LazyRelationshipCollection;

/**
 * Class LazyRelationshipCollectionTest
 * @package GraphAware\Neo4j\OGM\Tests\Lazy
 *
 * @group lazy
 */
class LazyRelationshipCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        $coll = new LazyRelationshipCollection();
        print_r($coll->first());
    }
}