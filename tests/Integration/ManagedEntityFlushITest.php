<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration;

use GraphAware\Neo4j\OGM\Tests\Integration\Model\User;

class ManagedEntityFlushITest extends IntegrationTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->clearDb();
    }

    /**
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
}