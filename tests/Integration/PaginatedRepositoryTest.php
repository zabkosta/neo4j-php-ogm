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
 * Class PaginatedRepositoryTest.
 *
 * @group paginated
 */
class PaginatedRepositoryTest extends IntegrationTestCase
{
    public function testPaginatedFindAll()
    {
        $this->clearDb();
        for ($i = 0; $i < 100; ++$i) {
            $user = new User('Login '.$i);
            $this->em->persist($user);
        }
        $this->em->flush();

        /** @var User[] $users */
        $users = $this->em->getRepository(User::class)->paginated(0, 10);
        $this->assertCount(10, $users);

        $users2 = $this->em->getRepository(User::class)->paginated(10, 90);
        $this->assertCount(90, $users2);
    }

    public function testPaginatedWithCustomOrder()
    {
        $this->clearDb();
        for ($i = 0; $i <= 100; ++$i) {
            $user = new User('Login '.$i);
            $user->setAge($i);
            $this->em->persist($user);
        }
        $this->em->flush();

        /** @var User[] $users */
        $users = $this->em->getRepository(User::class)->paginated(0, 10, ['age', BaseRepository::ORDER_DESC]);
        $this->assertCount(10, $users);
        $i = 100;
        foreach ($users as $user) {
            $this->assertEquals($i, $user->getAge());
            --$i;
        }
    }
}
