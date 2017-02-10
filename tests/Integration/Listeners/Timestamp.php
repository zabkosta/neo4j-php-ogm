<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\Listeners;

use GraphAware\Neo4j\OGM\Event\PreFlushEventArgs;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\User;

class Timestamp
{
    public function preFlush(PreFlushEventArgs $eventArgs)
    {
        $dt = new \DateTime("NOW", new \DateTimeZone("UTC"));

        foreach ($eventArgs->getEntityManager()->getUnitOfWork()->getNodesScheduledForCreate() as $entity) {
            if ($entity instanceof User) {
                $entity->setUpdatedAt($dt);
            }
        }
    }
}