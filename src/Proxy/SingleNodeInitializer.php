<?php

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
        $startId = $node->identity();
        $relQueryPart = $this->getRelQueryPart();

        $query = 'MATCH (start)'.$relQueryPart.'(n) WHERE id(start) = {startId} RETURN n';

        $result = $this->em->getDatabaseDriver()->run($query, ['startId' => $startId]);

        $object = $this->handleResult($result);
        $this->em->getRepository(get_class($baseInstance))->setInversedAssociation($baseInstance, $object, $this->relationshipMetadata->getPropertyName());

        return $object;

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

        return $this->em->getRepository($class)->hydrate($result->firstRecord(), false);
    }
}