<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration;

use GraphAware\Neo4j\OGM\Event\PreFlushEventArgs;
use GraphAware\Neo4j\OGM\Events;
use GraphAware\Neo4j\OGM\Tests\Integration\Listeners\Timestamp;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\User;

class EventListenerIntegrationTest extends IntegrationTestCase
{
    /**
     * @group pre-flush
     */
    public function testPreFlushEvent()
    {
        $this->clearDb();
        $this->em->getEventManager()->addEventListener(Events::PRE_FLUSH, new Timestamp());

        $user = new User("ikwattro");

        $this->em->persist($user);
        $this->em->flush();

        $this->assertNotNull($user->getUpdatedAt());
        var_dump($user->getUpdatedAt());
    }
}