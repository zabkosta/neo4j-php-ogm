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

use GraphAware\Neo4j\OGM\Tests\Integration\Model\User;

/**
 * Class ManagedEntityFlushITest.
 *
 * @group manage-flush
 */
class ManagedEntityFlushITest extends IntegrationTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->clearDb();
    }

    /**
     * @group flush-1
     * @group flush
     */
    public function testManagedEntityIsFlushedOnBooleanLabelUpdate()
    {
        $user = new User('ikwattro');
        $user->setActive();
        $this->em->persist($user);
        $this->em->flush();
        $this->assertGraphExist('(u:User:Active {login:"ikwattro"})');
        $user->setInactive();
        $this->assertFalse($user->isActive());
        $this->em->flush();
        $this->assertGraphNotExist('(u:User:Active {login:"ikwattro"})');
    }

    /**
     * @group flush
     */
    public function testManagedEntityChangesAreDetected()
    {
        $user = new User('ikwattro');
        $this->em->persist($user);
        $this->em->flush();
        $user->setAge(35);
        $this->em->flush();
        $this->assertGraphExist('(u:User {login:"ikwattro", age:35})');
    }

    /**
     * @group flush
     */
    public function testChangesComputedForEntityFetched()
    {
        $this->createUser();
        /** @var User $ikwattro */
        $ikwattro = $this->em->getRepository(User::class)->findOneBy('login', 'ikwattro');
        $ikwattro->setAge(35);
        $this->em->flush();
        $this->assertGraphExist('(u:User {login:"ikwattro", age: 35})');
    }

    private function createUser($login = 'ikwattro')
    {
        $user = new User($login);
        $this->em->persist($user);
        $this->em->flush();
        $this->assertGraphExist('(u:User {login:"'.$login.'"})');
        $this->em->clear();
    }
}
