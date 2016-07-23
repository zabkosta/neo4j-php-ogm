<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration;

use GraphAware\Neo4j\OGM\Tests\Integration\Model\City;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Company;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Movie;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Person;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Player;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Repository;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Team;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\User;

/**
 * Class OrderByIntegrationTest
 * @package GraphAware\Neo4j\OGM\Tests\Integration
 *
 * @group order-by-rel
 */
class OrderByIntegrationTest extends IntegrationTestCase
{
    public function testOrderByOnSimpleRelationship()
    {
        $this->clearDb();
        $person1 = new Person('Michal');
        $person2 = new Person('Alessandro');
        $person3 = new Person('Luanne');
        $movie = new Movie('Matrix Revolutions');
        $movie->players->add($person1);
        $movie->players->add($person2);
        $movie->players->add($person3);
        $person1->movies->add($movie);
        $person2->movies->add($movie);
        $person3->movies->add($movie);
        $this->em->persist($movie);
        $this->em->flush();

        $this->assertGraphExist('(m:Movie)<-[:PLAYED_IN]-(p:Person {name:"Michal"}), (m)<-[:PLAYED_IN]-(p2:Person {name:"Alessandro"}), (m)<-[:PLAYED_IN]-(p3:Person {name:"Luanne"})');
        $this->em->clear();

        /** @var Movie $m */
        $m = $this->em->getRepository(Movie::class)->findOneBy('title', 'Matrix Revolutions');
        $this->assertCount(3, $m->getPlayers());
        /** @var Person[] $players */
        $players = $m->getPlayers();
        $this->assertEquals('Alessandro', $players[0]->getName());
        $this->assertEquals('Luanne', $players[1]->getName());
        $this->assertEquals('Michal', $players[2]->getName());
    }

    public function testOrderByWithFindAll()
    {
        $this->clearDb();
        $person1 = new Person('Michal');
        $person2 = new Person('Alessandro');
        $person3 = new Person('Luanne');
        $person4 = new Person('Alessandra');
        $movie = new Movie('Matrix Revolutions');
        $movie->players->add($person1);
        $movie->players->add($person2);
        $movie->players->add($person3);
        $movie->players->add($person4);
        $person1->movies->add($movie);
        $person2->movies->add($movie);
        $person3->movies->add($movie);
        $person4->movies->add($movie);
        $this->em->persist($movie);
        $this->em->flush();

        $this->assertGraphExist('(m:Movie)<-[:PLAYED_IN]-(p:Person {name:"Michal"}), (m)<-[:PLAYED_IN]-(p2:Person {name:"Alessandro"}), (m)<-[:PLAYED_IN]-(p3:Person {name:"Luanne"})');
        $this->em->clear();

        /** @var Movie[] $movies */
        $movies = $this->em->getRepository(Movie::class)->findAll();
        foreach ($movies as $m) {
            $this->assertCount(4, $m->getPlayers());
            /** @var Person[] $players */
            $players = $m->getPlayers();
            $this->assertEquals('Alessandra', $players[0]->getName());
            $this->assertEquals('Luanne', $players[2]->getName());
            $this->assertEquals('Michal', $players[3]->getName());
            $this->assertEquals('Alessandro', $players[1]->getName());
        }

    }

    /**
     * @group order-re-end
     */
    public function testOrderByOnRelationshipEntitiesBoundedNodes()
    {
        $this->clearDb();
        $player1 = new Player('PlayerAA');
        $player2 = new Player('PlayerBC');
        $player3 = new Player('PlayerAB');
        $player4 = new Player('PlayerAD');
        $player5 = new Player('PlayerBA');
        $team = new Team('RedBull');
        $player1->addToTeam($team);
        $player2->addToTeam($team);
        $player3->addToTeam($team);
        $player4->addToTeam($team);
        $player5->addToTeam($team);
        $this->em->persist($team);
        $this->em->flush();
        $this->em->clear();

        /** @var Team $team */
        $team = $this->em->getRepository(Team::class)->findOneBy('name', 'RedBull');
        $this->assertCount(5, $team->getMemberships());
        $this->assertEquals('PlayerAA', $team->getMemberships()[0]->getPlayer()->getName());
        $this->assertEquals('PlayerAB', $team->getMemberships()[1]->getPlayer()->getName());
        $this->assertEquals('PlayerBC', $team->getMemberships()[4]->getPlayer()->getName());
    }

    /**
     *
     * @group order-re-prop
     */
    public function testOrderByOnRelationshipEntityProperties()
    {
        $this->clearDb();
        $a = new User('ikwattro');
        $b = new User('jexp');
        $c = new User('luanne');
        $r = new Repository('neo4j/neo4j');
        $a->addContributionTo($r, 10);
        $b->addContributionTo($r, 500);
        $c->addContributionTo($r, 30);
        $this->em->persist($r);
        $this->em->flush();
        $this->em->clear();

        /** @var Repository $repository */
        $repository = $this->em->getRepository(Repository::class)->findOneBy('name', 'neo4j/neo4j');
        $this->assertCount(3, $repository->getContributions());
        $this->assertEquals(500, $repository->getContributions()[0]->getScore());
        $this->assertEquals(30, $repository->getContributions()[1]->getScore());
        $this->assertEquals(10, $repository->getContributions()[2]->getScore());
    }

    /** @group order-re-prop */
    public function testOrderyByWithFindAll()
    {
        $this->clearDb();
        $a = new User('ikwattro');
        $b = new User('jexp');
        $c = new User('luanne');
        $r = new Repository('neo4j/neo4j');
        $a->addContributionTo($r, 10);
        $b->addContributionTo($r, 500);
        $c->addContributionTo($r, 30);
        $this->em->persist($r);
        $this->em->flush();
        $this->em->clear();
        /** @var Repository[] $repository */
        $repositories = $this->em->getRepository(Repository::class)->findAll();
        /** @var Repository $repository */
        foreach ($repositories as $repository) {
            $this->assertCount(3, $repository->getContributions());
            $this->assertEquals(500, $repository->getContributions()[0]->getScore());
            $this->assertEquals(30, $repository->getContributions()[1]->getScore());
            $this->assertEquals(10, $repository->getContributions()[2]->getScore());
        }
    }

    /**
     * @group order-re-prop-lazy
     */
    public function testOrderByRelationshipEntityPropertiesLazyLoaded()
    {
        $this->clearDb();
        $a = new User('ikwattro');
        $b = new User('jexp');
        $c = new User('luanne');
        $city = new City('London');
        $a->setCity($city, 456790);
        $b->setCity($city, 456789);
        $c->setCity($city, 456791);
        $this->em->persist($city);
        $this->em->flush();
        $this->em->clear();

        /** @var City $city */
        $city = $this->em->getRepository(City::class)->findOneBy('name', 'London');
        $this->assertCount(3, $city->getHabitants());
        $this->assertEquals(456789, $city->getHabitants()[2]->getSince());
        $this->assertEquals(456790, $city->getHabitants()[1]->getSince());
        $this->assertEquals(456791, $city->getHabitants()[0]->getSince());

    }
}