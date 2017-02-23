<?php

namespace GraphAware\Neo4j\OGM\Proxy;

use Doctrine\Common\Collections\ArrayCollection;
use GraphAware\Common\Result\Result;
use GraphAware\Common\Type\Node;

class NodeCollectionInitializer extends SingleNodeInitializer
{
    public function initialize(Node $node, $baseInstance)
    {
        $startId = $node->identity();
        $relQueryPart = $this->getRelQueryPart();

        $query = 'MATCH (start) WHERE id(start) = {startId} 
        OPTIONAL MATCH (start)'.$relQueryPart.'(target) 
        RETURN target AS '.$this->relationshipMetadata->getPropertyName();

        $result = $this->em->getDatabaseDriver()->run($query, ['startId' => $startId]);

        $otherInstances = $this->handleResult($result);
        $repository = $this->em->getRepository(get_class($baseInstance));
        foreach ($otherInstances as $instance) {
            $repository->setInversedAssociation($baseInstance, $instance, $this->relationshipMetadata->getPropertyName());
        }

        return $otherInstances;
    }

    public function handleResult(Result $result)
    {
        $instances = new ArrayCollection();
        $class = $class = $this->relationshipMetadata->getDirection() === 'INCOMING' ? $this->metadata->getClassName() : $this->relationshipMetadata->getTargetEntity();
        foreach ($result->records() as $record) {
            $instances->add($this->em->getRepository($class)->hydrate($record, false, $this->relationshipMetadata->getPropertyName()));
        }

        return $instances;
    }

}