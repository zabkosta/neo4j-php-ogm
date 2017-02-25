<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\Community;

use GraphAware\Neo4j\OGM\Tests\Integration\IntegrationTestCase;
use GraphAware\Neo4j\OGM\Tests\Integration\Models\EntityWithSimpleRelationship\Car;
use GraphAware\Neo4j\OGM\Tests\Integration\Models\EntityWithSimpleRelationship\ModelNumber;
use GraphAware\Neo4j\OGM\Tests\Integration\Models\EntityWithSimpleRelationship\Person;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class GithubIssue49 extends IntegrationTestCase
{
    /**
     * When the items are new we want to make sure to cascade persist to all relations.
     */
    public function testCascadePersistOnCreate()
    {
        // Clear database
        $this->clearDb();

        $person = new Person('Mike');
        $car = new Car('Bugatti', $person);
        $person->setCar($car);
        $car->setModelNumber(new ModelNumber('Foobar'));
        $this->em->persist($person);
        $this->em->flush();

        $result = $this->client->run('MATCH (c:Car {model:"Bugatti"})-[:HAS_MODEL_NUMBER]->(m:ModelNumber {number:"Foobar"}) RETURN m, c');
        $this->assertEquals(1, $result->size());
    }

    /**
     * When we do a simple update on one entity we do NOT want to fetch related entities from the
     * database and persist them as well. Unless they are already in memory.
     */
    public function testNoCascadeOnExistingItems()
    {
        // Clear database
        $this->clearDb();

        $person = new Person('Mike');
        $car = new Car('Bugatti', $person);
        $person->setCar($car);
        $car->setModelNumber(new ModelNumber('Foobar'));
        $this->em->persist($person);
        $this->em->flush();

        $this->resetEm();
        $persons = $this->em->getRepository(Person::class)->findBy(['name' => 'Mike']);
        /** @var Person $person */
        $person = $persons[0];
        $person->setName('Tom');

        // TODO make sure it does not fetch Car or ModelNumber from db.
        $this->em->persist($person);
        $this->em->flush();
    }
}
