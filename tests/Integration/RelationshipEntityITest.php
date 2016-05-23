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
        /** @var Person $tom */
        $tom = $this->getPerson('Tom Hanks');
        foreach ($tom->getRoles() as $role) {
            $this->assertInstanceOf(Role::class, $role);
            $this->assertEquals($tom->id, $role->getActor()->getId());
            $this->assertInstanceOf(Movie::class, $role->getMovie());
            $this->assertInternalType('array', $role->getRoles());
        }
    }

    public function testRelationshipEntitiesShouldBeManaged()
    {
        $tom = $this->getPerson('Tom Hanks');
        foreach ($tom->getRoles() as $role) {
            $role->setRoles(array('Super Tom'));
        }
        $this->em->flush();
        $this->em->clear();

        $tom2 = $this->getPerson('Tom Hanks');
        foreach ($tom2->getRoles() as $role) {
            $this->assertEquals('Super Tom', $role->getRoles()[0]);
        }
    }

    public function testRelationshipEntityCanBeRemoved()
    {
        $tom = $this->getPerson('Tom Hanks');
        $c = count($tom->getRoles());
        foreach ($tom->getRoles() as $role) {
            echo $role->getId();
            $tom->getRoles()->removeElement($role);
            break;
        }
        $this->em->flush();
        $this->em->clear();
        $tom = $this->getPerson('Tom Hanks');
        $this->assertEquals($c-1, count($tom->getRoles()));
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