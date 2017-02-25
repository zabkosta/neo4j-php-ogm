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

    public function testRatingCanBetweenGuestAndHotelIsCreated()
    {
        $guest = new Guest('john');
        $hotel = new Hotel('Crowne');
        $rating = new Rating($guest, $hotel, 3.5);
        $this->em->persist($guest);
    }
}