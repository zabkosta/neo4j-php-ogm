<?php

namespace GraphAware\Neo4j\Client\Tests\Integration;

use GraphAware\Neo4j\OGM\Tests\Integration\IntegrationTestCase;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Person;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Role;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Movie;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\ScoreRel;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Score;

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

    /**
     *
     * @group rel-entity-fetch
     */
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

    /**
     * @group re-remove
     */
    public function testRelationshipEntityCanBeRemoved()
    {
        $tom = $this->getPerson('Tom Hanks');
        $c = count($tom->getRoles());
        foreach ($tom->getRoles() as $role) {
            $tom->getRoles()->removeElement($role);
            break;
        }
        $this->em->flush();
        $this->em->clear();
        $tom = $this->getPerson('Tom Hanks');
        $this->assertEquals($c-1, count($tom->getRoles()));
    }

    /**
     * @group re-cascade-persist
     */
    public function testRelationshipEntityCanBeAdded()
    {
        $this->clearDb();
        $person = new Person('ikwattro');
        $movie = new Movie('Neo4j on the rocks');
        $person->addRole($movie, ['Super Actor']);
        $this->em->persist($person);
        $this->em->flush();
        $this->assertGraphExist('(p:Person {name:"ikwattro"})-[r:ACTED_IN {roles: ["Super Actor"]}]->(m:Movie {title:"Neo4j on the rocks"})');
    }

    /**
     * @group re-ffetch
     */
    public function testRelationshipEntityAndFindAllFetch()
    {
        $actors = $this->em->getRepository(Person::class)->findAll();
        foreach ($actors as $actor) {
            $this->assertInstanceOf(Person::class, $actor);
        }
    }

    /**
     * @throws \Exception
     * @throws \GraphAware\Neo4j\Client\Exception\Neo4jException
     *
     * @group re-single
     */
    public function testSingleRelationshipEntityIsFetched()
    {
        $this->clearDb();
        $this->playMovies();
        $this->client->run("MATCH (m:Movie {title:'The Matrix'})
        CREATE (m)-[:HAS_SCORE {finalScore: 6.74}]->(:Score {value: 7})");
        /** @var Movie $matrix */
        $matrix = $this->em->getRepository(Movie::class)->findOneBy('title', 'The Matrix');
        $this->assertInstanceOf(ScoreRel::class, $matrix->getScore());
        $this->assertInstanceOf(Score::class, $matrix->getScore()->getScore());
        $this->assertEquals(6.74, $matrix->getScore()->getFinalScore());
    }

    /**
     * @throws \GraphAware\Neo4j\Client\Exception\Neo4jException
     *
     * @group re-single-all
     */
    public function testSingleRelEntityAndFindAll()
    {
        $this->clearDb();
        $this->playMovies();
        $this->client->run("MATCH (m:Movie {title:'The Matrix'})
        CREATE (m)-[:HAS_SCORE {finalScore: 6.74}]->(:Score {value: 7})");
        /** @var Movie[] $movies */
        $movies = $this->em->getRepository(Movie::class)->findAll();
        foreach ($movies as $movie) {
            if ($movie->title === 'The Matrix') {
                $this->assertInstanceOf(ScoreRel::class, $movie->getScore());
                $this->assertEquals(6.74, $movie->getScore()->getFinalScore());
            }
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