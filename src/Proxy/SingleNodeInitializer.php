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

use GraphAware\Common\Result\Result;
use GraphAware\Common\Type\Node;
use GraphAware\Neo4j\OGM\EntityManager;
use GraphAware\Neo4j\OGM\Metadata\NodeEntityMetadata;
use GraphAware\Neo4j\OGM\Metadata\RelationshipMetadata;

class SingleNodeInitializer
{
    protected $em;

    protected $relationshipMetadata;

    protected $metadata;

    public function __construct(EntityManager $em, RelationshipMetadata $relationshipMetadata, NodeEntityMetadata $nodeEntityMetadata)
    {
        $this->em = $em;
        $this->relationshipMetadata = $relationshipMetadata;
        $this->metadata = $nodeEntityMetadata;
    }

    public function initialize(Node $node, $baseInstance)
    {
        $persister = $this->em->getEntityPersister($this->metadata->getClassName());
        $persister->getSimpleRelationship($this->relationshipMetadata->getPropertyName(), $baseInstance);
    }

    protected function getRelQueryPart()
    {
        switch ($this->relationshipMetadata->getDirection()) {
            case 'INCOMING':
                $relStr = '<-[rel_%s:%s]-';
                break;
            case 'OUTGOING':
                $relStr = '-[rel_%s:%s]->';
                break;
            default:
                $relStr = '-[rel_%s:%s]-';
                break;
        }

        $relationshipType = $this->relationshipMetadata->getType();
        $relQueryPart = sprintf($relStr, strtolower($relationshipType), $relationshipType);

        return $relQueryPart;
    }

    public function handleResult(Result $result)
    {
        if ($result->size() === 0) {
            return null;
        }

        if ($result->size() > 1) {
            throw new \RuntimeException(sprintf('Expected only 1 result, got %d', $result->size()));
        }

        $class = $this->relationshipMetadata->getDirection() === 'INCOMING' ? $this->metadata->getClassName() : $this->relationshipMetadata->getTargetEntity();
        $cm = $this->em->getClassMetadata($class);

        if (count($cm->getRelationships()) > 0) {
            $o = $this->em->getProxyFactory($cm)->fromNode($result->firstRecord()->get('n'), array($this->relationshipMetadata->getMappedByProperty()));
            $this->em->getHydrator($this->metadata->getClassName())->populateDataToInstance($result->firstRecord()->get('n'), $this->metadata, $o);

            return $o;
        }

        return $this->em->getRepository($class)->hydrate($result->firstRecord(), false);
    }
}