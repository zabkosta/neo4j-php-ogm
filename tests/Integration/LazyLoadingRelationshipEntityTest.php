<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\Tests\Integration;

use GraphAware\Neo4j\OGM\Lazy\LazyRelationshipCollection;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\City;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\LivesIn;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\User;

/**
 * Class LazyLoadingRelationshipEntity.
 *
 * @group lazy-re
 */
class LazyLoadingRelationshipEntityTest extends IntegrationTestCase
{
    public function testRelationshipEntitiesAreLazyLoaded()
    {
        $this->clearDb();
        $michal = new User('michal');
        $daniela = new User('daniela');
        $city = new City('London');

        $michal->setCity($city);
        $daniela->setCity($city);
        $this->em->persist($city);
        $this->em->persist($michal);
        $this->em->persist($daniela);
        $this->em->flush();

        $this->assertGraphExist('(m:User {login:"michal"})-[:LIVES_IN {since:123}]->(c:City {name:"London"})<-[:LIVES_IN {since:123}]-(d:User {login:"daniela"})');
        $this->em->clear();

        /** @var City $london */
        $london = $this->em->getRepository(City::class)->findOneBy('name', 'London');
        $this->assertInstanceOf(LazyRelationshipCollection::class, $london->getHabitants());

        $this->assertCount(2, $london->getHabitants());
    }

    public function testLazyLoadedEntitiesAreManagedForRemoval()
    {
        $this->clearDb();
        $michal = new User('michal');
        $daniela = new User('daniela');
        $city = new City('London');

        $michal->setCity($city);
        $daniela->setCity($city);
        $this->em->persist($city);
        $this->em->persist($michal);
        $this->em->persist($daniela);
        $this->em->flush();

        $this->assertGraphExist('(m:User {login:"michal"})-[:LIVES_IN {since:123}]->(c:City {name:"London"})<-[:LIVES_IN {since:123}]-(d:User {login:"daniela"})');
        $this->em->clear();

        /** @var City $london */
        $london = $this->em->getRepository(City::class)->findOneBy('name', 'London');
        $this->assertInstanceOf(LazyRelationshipCollection::class, $london->getHabitants());

        $this->assertCount(2, $london->getHabitants());
        /** @var LivesIn $livesIn */
        foreach ($london->getHabitants() as $livesIn) {
            $this->assertInstanceOf(User::class, $livesIn->getUser());
            $this->assertInstanceOf(City::class, $livesIn->getCity());
            $this->assertEquals(123, $livesIn->getSince());
            $this->assertInstanceOf(LivesIn::class, $livesIn->getUser()->getLivesIn());
        }

        $u = $london->getHabitants()[0];
        $london->getHabitants()->removeElement($u);
        $u->getUser()->removeCity($u->getCity());
        $this->em->flush();
    }
}
