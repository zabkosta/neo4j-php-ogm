<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration;

use GraphAware\Neo4j\OGM\Tests\Integration\Models\SimpleRelationshipEntity\Guest;
use GraphAware\Neo4j\OGM\Tests\Integration\Models\SimpleRelationshipEntity\Hotel;
use GraphAware\Neo4j\OGM\Tests\Integration\Models\SimpleRelationshipEntity\Rating;

/**
 * Class SimpleRelationshipEntityTest
 * @package GraphAware\Neo4j\OGM\Tests\Integration
 *
 * @group simple-re
 */
class SimpleRelationshipEntityTest extends IntegrationTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->clearDb();
    }

    /**
     * @group simple-re-1
     */
    public function testRatingCanBetweenGuestAndHotelIsCreated()
    {
        $guest = new Guest('john');
        $hotel = new Hotel('Crowne');
        $rating = new Rating($guest, $hotel, 3.5);
        $guest->setRating($rating);
        $this->em->persist($guest);
        $this->em->persist($rating);
        $this->em->flush();
        $this->assertGraphExist('(g:Guest {name:"john"})-[:RATED {score: 3.5}]->(h:Hotel {name:"Crowne"})');
        $this->assertNotNull($rating->getId());
    }

    public function testRatingCanBeAddedOnManagedEntities()
    {
        $guest = new Guest('john');
        $hotel = new Hotel('Crowne');
        $this->persist($guest, $hotel);
        $this->em->flush();
        $this->em->clear();

        /** @var Guest $john */
        $john = $this->em->getRepository(Guest::class)->findOneBy(['name' => 'john']);
        /** @var Hotel $crowne */
        $crowne = $this->em->getRepository(Hotel::class)->findOneBy(['name' => 'Crowne']);

        $rating = new Rating($john, $crowne, 4.8);
        $john->setRating($rating);
        $this->em->flush();
        $this->assertGraphExist('(g:Guest {name:"john"})-[:RATED {score: 4.8}]->(h:Hotel {name:"Crowne"})');
    }

    public function testRatingIsRemovedWhenUnreferenced()
    {
        $guest = new Guest('john');
        $hotel = new Hotel('Crowne');
        $rating = new Rating($guest, $hotel, 3.5);
        $guest->setRating($rating);
        $this->em->persist($guest);
        $this->em->flush();
        $this->assertGraphExist('(g:Guest {name:"john"})-[:RATED {score: 3.5}]->(h:Hotel {name:"Crowne"})');
        $guest->setRating(null);
        $hotel->setRating(null);
        $this->em->remove($rating);
        $this->em->flush();
    }
}