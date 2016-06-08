<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration;

use GraphAware\Neo4j\OGM\Repository\BaseRepository;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\BothTest;

/**
 * Class RelationshipBothITest
 * @package GraphAware\Neo4j\OGM\Tests\Integration
 *
 * @group rel-both
 */
class RelationshipBothITest extends IntegrationTestCase
{
    public function testBothRelationshipsFetch()
    {
        $this->clearDb();
        $this->createGraph();

        $repository = $this->em->getRepository(BothTest::class);
        $entities = $repository->findAll(['order' => ['name' => BaseRepository::ORDER_ASC]]);
        $this->assertEquals('a', $entities[0]->getName());
        $this->assertEquals('c', $entities[2]->getName());
        $a = $entities[0];
        $this->assertCount(2, $a->getOthers());
        foreach (['c', 'b'] as $i) {
            $this->assertTrue($a->hasOtherWithName($i));
        }
    }

    private function createGraph()
    {
        $query = 'CREATE (a:Both {name:"a"})-[:RELATES]->(b:Both {name:"b"}), (a)<-[:RELATES]-(c:Both {name:"c"})';
        $this->client->run($query);
    }
}