<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration;

use GraphAware\Neo4j\OGM\Tests\Integration\Models\RelationshipCollection\Building;
use GraphAware\Neo4j\OGM\Tests\Integration\Models\RelationshipCollection\Floor;

/**
 * Class EntityWithSimpleRelationshipCollectionTest
 * @package GraphAware\Neo4j\OGM\Tests\Integration
 *
 * @group entity-simple-relcollection
 */
class EntityWithSimpleRelationshipCollectionTest extends IntegrationTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->clearDb();
    }

    public function testBuildingCanBeCreated()
    {
        $building = new Building();
        $this->em->persist($building);
        $this->em->flush();

        $result = $this->client->run('MATCH (n:Building) RETURN n');
        $this->assertEquals(1, $result->size());
    }

    public function testBuildingWithFloorsCanBeCreated()
    {
        $building = new Building();
        $floor1 = new Floor(1);
        $building->getFloors()->add($floor1);
        $this->em->persist($building);
        $this->em->flush();

        $result = $this->client->run('MATCH (n:Building)-[:HAS_FLOOR]->(f:Floor {level: 1}) RETURN n, f');
        $this->assertEquals(1, $result->size());
    }

    public function testBuildingWithFloorsCanBeLoaded()
    {
        $building = new Building();
        $floor1 = new Floor(1);
        $building->getFloors()->add($floor1);
        $this->em->persist($building);
        $this->em->flush();
        $this->em->clear();

        $entities = $this->em->getRepository(Building::class)->findAll();
        /** @var Building $b */
        $b = $entities[0];
        $this->assertInstanceOf(Building::class, $b);
        $floors = $b->getFloors();
        $this->assertCount(1, $floors);
        /** @var Floor $floor */
        $floor = $floors[0];
        $this->assertEquals(spl_object_hash($b), spl_object_hash($floor->getBuilding()));
    }
}