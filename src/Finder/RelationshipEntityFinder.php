<?php

namespace GraphAware\Neo4j\OGM\Finder;

use GraphAware\Common\Cypher\Statement;
use GraphAware\Common\Result\Result;

class RelationshipEntityFinder extends RelationshipsFinder
{
    protected $relationshipEntityMetadata;

    protected $baseInstance;

    public function __construct(\GraphAware\Neo4j\OGM\EntityManager $em, $className, \GraphAware\Neo4j\OGM\Metadata\RelationshipMetadata $relationshipMetadata, $baseInstance)
    {
        parent::__construct($em, $className, $relationshipMetadata);
        $this->relationshipEntityMetadata = $this->em->getRelationshipEntityMetadata($relationshipMetadata->getRelationshipEntityClass());
        $this->baseInstance = $baseInstance;
    }

    protected function hydrateResult(Result $result)
    {
        $startNodeMetadata = $this->em->getClassMetadataFor($this->relationshipEntityMetadata->getStartNode());
        $endNodeMetadata = $this->em->getClassMetadataFor($this->relationshipEntityMetadata->getEndNode());

        $repo = $this->em->getRepository(get_class($this->baseInstance));
        $identifier = 'rel_' . $this->relationshipMetadata->getPropertyName() . '_' . $this->relationshipEntityMetadata->getType();
        $instances = [];

        foreach ($result->records() as $record) {
            foreach ( $record->get($identifier) as $i) {
                $instances[] = $repo->hydrateRelationshipEntity($this->relationshipEntityMetadata, $i, $startNodeMetadata, $endNodeMetadata, $this->baseInstance, $this->relationshipMetadata, $this->baseInstance->getId());
            }
        }

        return $instances;
    }


    public function buildStatement($fromId, $direction, $type, $identifier)
    {
        $type = $this->relationshipEntityMetadata->getType();
        $identifier = 'rel_' . $this->relationshipMetadata->getPropertyName() . '_' . $this->relationshipEntityMetadata->getType();

        switch ($direction) {
            case 'INCOMING':
                $pattern = '<-[%s:%s]-';
                break;
            case 'OUTGOING':
                $pattern = '-[%s:%s]->';
                break;
            case 'BOTH':
                $pattern = '-[%s:%s]->';
                break;
            default:
                throw new \LogicException(sprintf('Unsupported relationship direction "%s"', $direction));
        }

        $relationshipPattern = sprintf($pattern, $identifier, $type);

        $query = 'MATCH (start) WHERE id(start) = {id}
        MATCH (start)'.$relationshipPattern.'(end)';

        if ($this->relationshipMetadata->hasOrderBy()) {
            $orderProperty = $this->relationshipMetadata->getOrderByPropery();
            $split = explode('.', $orderProperty);
            $reMetadata = $this->relationshipEntityMetadata;
            if (count($split) > 1) {
                $reName = $split[0];
                $v = $split[1];
                if ($reMetadata->getStartNodePropertyName() === $reName || $reMetadata->getEndNodePropertyName() === $reName) {
                    $orderProperty = $this->relationshipMetadata->getPropertyName() . '.' . $v;
                }
            } else {
                if (null !== $reMetadata->getPropertyMetadata($this->relationshipMetadata->getOrderByPropery())) {
                    $orderProperty = $identifier . '.' . $this->relationshipMetadata->getOrderByPropery();
                }
            }
            $query .= ' WITH end, ' . $identifier . ' ORDER BY ' . $orderProperty . ' ' . $this->relationshipMetadata->getOrder();
        }

        $query .= ' RETURN CASE count('.$identifier.') WHEN 0 THEN [] ELSE collect({start:startNode('.$identifier.'), end:endNode('.$identifier.'), rel:'.$identifier.'}) END AS ' . $identifier;

        //print_r($query);

        return Statement::create($query, ['id' => $fromId]);
    }

}