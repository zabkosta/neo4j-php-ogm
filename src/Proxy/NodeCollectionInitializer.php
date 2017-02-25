<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\Proxy;

use Doctrine\Common\Collections\ArrayCollection;
use GraphAware\Common\Result\Result;
use GraphAware\Common\Type\Node;

class NodeCollectionInitializer extends SingleNodeInitializer
{
    public function initialize(Node $node, $baseInstance)
    {
        $persister = $this->em->getEntityPersister($this->metadata->getClassName());
        $persister->getSimpleRelationshipCollection($this->relationshipMetadata->getPropertyName(), $baseInstance);
    }

    public function handleResult(Result $result)
    {
        $instances = new ArrayCollection();
        $class = $class = $this->relationshipMetadata->getDirection() === 'INCOMING' ? $this->metadata->getClassName() : $this->relationshipMetadata->getTargetEntity();
        $cm = $this->em->getClassMetadata($class);
        foreach ($result->records() as $record) {
            $o = count($cm->getRelationships()) > 1
                ? $this->em->getProxyFactory($cm)->fromNode($record->get($this->relationshipMetadata->getPropertyName()))
                : $this->em->getRepository($class)->hydrate($record, false, $this->relationshipMetadata->getPropertyName());
            $otherNodeMeta = $this->em->getClassMetadataFor($this->relationshipMetadata->getTargetEntity());
            $this->em->getHydrator($class)->populateDataToInstance($record->get($this->relationshipMetadata->getPropertyName()), $otherNodeMeta, $o);
            $instances->add($o);
        }

        return $instances;
    }

}