<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\tests\Integration;

use GraphAware\Neo4j\OGM\Repository\BaseRepository;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\User;

/**
 * Class RepositoryITest.
 *
 * @group filter
 */
class RepositoryFilteringITest extends IntegrationTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->clearDb();
    }

    public function testLimitCanBeAddedToFindAll()
    {
        for ($i = 1; $i <= 10; ++$i) {
            $user = new User(sprintf('login%d', $i));
            $this->em->persist($user);
        }
        $this->em->flush();
        $this->em->clear();

        $users = $this->em->getRepository(User::class)->findAll();
        $this->assertCount(10, $users);
        unset($users);

        $filtered = $this->em->getRepository(User::class)->findAll(['limit' => 5]);
        $this->assertCount(5, $filtered);
    }

    /**
     * @group filter-order
     */
    public function testOrderFilterCanBeAddedToFindAll()
    {
        for ($i = 1; $i <= 9; ++$i) {
            $user = new User(sprintf('login%d', $i));
            $this->em->persist($user);
        }
        $this->em->flush();
        $this->em->clear();

        $users = $this->em->getRepository(User::class)->findAll(['order' => ['login' => BaseRepository::ORDER_ASC]]);
        for ($i = 1; $i <= 9; ++$i) {
            $this->assertEquals(sprintf('login%d', $i), $users[$i - 1]->getLogin());
        }
    }
}
