<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration;

use GraphAware\Neo4j\OGM\Query;
use GraphAware\Neo4j\OGM\Tests\Integration\Models\MoviesDemo\Movie;
use GraphAware\Neo4j\OGM\Tests\Integration\Models\MoviesDemo\Person;

/**
 * @group complex-query
 */
class ComplexQueryResultTest extends IntegrationTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->clearDb();
        $this->playMovies();
    }

    public function testQueryReturningMap()
    {
        $q = $this->em->createQuery('MATCH (n:Person)-[r:ACTED_IN]->(m)
        RETURN n, {roles: r.roles, movie: m} AS actInfo LIMIT 2');

        $q->addEntityMapping('n', Person::class);
        $q->addEntityMapping('actInfo', null, Query::HYDRATE_MAP);
        $q->addEntityMapping('movie', Movie::class);

        $result = $q->getResult();
        $this->assertCount(2, $result);
        $row = $result[0];

        $this->assertInstanceOf(Person::class, $row['n']);
        $this->assertInternalType('array', $row['actInfo']);
        $this->assertInternalType('array', $row['actInfo']['roles']);
        $this->assertInstanceOf(Movie::class, $row['actInfo']['movie']);
    }

    public function testQueryReturningMapCollection()
    {
        $q = $this->em->createQuery('MATCH (n:Person)-[r:ACTED_IN]->(m) 
        WITH n, {roles: r.roles, movie: m} AS actInfo 
        RETURN n, collect(actInfo) AS actorInfos LIMIT 2');

        $q->addEntityMapping('n', Person::class);
        $q->addEntityMapping('actorInfos', null, Query::HYDRATE_MAP_COLLECTION);
        $q->addEntityMapping('movie', Movie::class);

        $result = $q->getResult();
        $this->assertCount(2, $result);
        $this->assertInstanceOf(Movie::class, $result[0]['actorInfos'][0]['movie']);


    }
}