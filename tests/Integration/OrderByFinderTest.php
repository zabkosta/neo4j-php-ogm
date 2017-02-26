<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration;

use GraphAware\Neo4j\OGM\Tests\Integration\Models\Base\User;

/**
 * @group order-by-finder
 */
class OrderByFinderTest extends IntegrationTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->clearDb();
    }

    public function testEntitiesAreOrderedWithFinderMethod()
    {
        for ($i = 1000; $i >= 1; --$i) {
            $user = new User($i);
            $this->em->persist($user);
        }
        $this->em->flush();
        $this->em->clear();
        $this->assertNodesCount(1000);

        $users = $this->em->getRepository(User::class)->findBy([], ['login' => 'ASC']);
        $this->assertCount(1000, $users);

        for ($i = 1; $i <= 1000; ++$i) {
            $u = $users[$i - 1];
            $this->assertEquals($i, $u->getLogin());
        }
    }
}
