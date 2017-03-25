<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration;

use GraphAware\Neo4j\OGM\Tests\Integration\Models\Tree\Level;

/**
 *
 * @group query-native
 */
class QueryTest extends IntegrationTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->clearDb();
        $this->createTree();
    }

    public function testCreateQueryReturnsEntities()
    {
        $q = $this->em->createQuery('MATCH (n:Level) WHERE n.code = {code} MATCH (n)-[:PARENT_LEVEL*0..]->(level) RETURN level');
        $q->addEntityMapping('level', Level::class);
        $q->setParameter('code', 'l3a');

        /** @var Level[] $levels */
        $levels = $q->execute();
        $this->assertCount(4, $levels);
        $this->assertEquals('root', $levels[3]->getCode());
        $this->assertCount(2, $levels[3]->getChildren());
    }

    private function createTree()
    {
        /**
         * (root)
         * (root)-(l1a)  (root)-(l1b)
         * (l1a)-(l2a)   (l1a)-(l2b)
         * (l1b)-(l2c)   (l1b)-(l2d)
         * (l2c)-(l3a)
         */
        $root = new Level('root');
        $l1a = new Level('l1a');
        $l1a->setParent($root);
        $l1b = new Level('l1b');
        $l1b->setParent($root);
        $l2a = new Level('l2a');
        $l2b = new Level('l2b');
        $l2a->setParent($l1a);
        $l2b->setParent($l1a);
        $l2c = new Level('l2c');
        $l2d = new Level('l2d');
        $l2c->setParent($l1b);
        $l2d->setParent($l1b);
        $l3a = new Level('l3a');
        $l3a->setParent($l2c);
        $this->em->persist($root);
        $this->em->flush();
        $this->em->clear();
    }
}