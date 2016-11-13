<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration;

use GraphAware\Neo4j\OGM\Tests\Integration\Model\AuthUser;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Movie;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\User;

class UnitOfWorkTest extends IntegrationTestCase
{
    public function testContains()
    {
        $user = new User('neo', 33);
        $this->assertFalse($this->em->contains($user));

        $this->em->persist($user);
        $this->assertTrue($this->em->contains($user));

        $this->em->flush();
        $this->assertTrue($this->em->contains($user));
    }

    public function testDetach()
    {
        $user = new User('neo', 33);
        $friend = new User('Trinity', 31);
        $user->addLovedBy($friend);
        $this->em->persist($user);
        $this->em->persist($user);
        $this->em->flush();

        $this->em->detach($user);

        $this->assertFalse($this->em->contains($user));
        $this->assertFalse($this->em->contains($friend));
    }
}
