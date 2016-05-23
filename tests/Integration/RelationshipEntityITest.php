<?php

namespace GraphAware\Neo4j\Client\Tests\Integration;

use GraphAware\Neo4j\OGM\Tests\Integration\IntegrationTestCase;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Person;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Role;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Movie;

/**
 * Class RelationshipEntityITest
 * @package GraphAware\Neo4j\Client\Tests\Integration
 *
 * @group rel-entity
 */
class RelationshipEntityITest extends IntegrationTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->playMovies();
    }

    public function testRelationshipEntitesAreRetrieved()
    {
        $tom = $this->getPerson('Tom Hanks');
        foreach ($tom->roles as $role) {
            $this->assertInstanceOf(Role::class, $role);
            $this->assertEquals($tom->id, $role->getActor()->getId());
            $this->assertInstanceOf(Movie::class, $role->getMovie());
            $this->assertInternalType('array', $role->getRoles());
        }
    }

    /**
     * @param string $name
     * @return Person
     * @throws \Exception
     */
    private function getPerson($name)
    {
        return $this->em->getRepository(Person::class)->findOneBy('name', $name);
    }
}