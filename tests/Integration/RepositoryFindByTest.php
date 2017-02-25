<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration;

use GraphAware\Neo4j\OGM\Tests\Integration\Models\Base\User;

/**
 * Class RepositoryFindByTest
 * @package GraphAware\Neo4j\OGM\Tests\Integration
 *
 * @group repository-find-by
 */
class RepositoryFindByTest extends IntegrationTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->clearDb();
    }

    public function testFindUsersByAge()
    {
        $user1 = new User('user1', 32);
        $user2 = new User('user2', 41);
        $user3 = new User('user3', 41);
        $this->persist($user1, $user2, $user3);
        $this->em->flush();
        $this->em->clear();

        $users = $this->em->getRepository(User::class)->findBy(['age' => 41]);
        $this->assertCount(2, $users);
    }
}