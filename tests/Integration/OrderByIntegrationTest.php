<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration;

use GraphAware\Neo4j\OGM\Tests\Integration\Model\Movie;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Person;

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
}