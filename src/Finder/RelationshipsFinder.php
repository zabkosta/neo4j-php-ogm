<?php

namespace GraphAware\Neo4j\OGM\Finder;

use GraphAware\Common\Cypher\Statement;
use GraphAware\Neo4j\OGM\EntityManager;
use GraphAware\Neo4j\OGM\Metadata\RelationshipMetadata;

class RelationshipsFinder
{
    private $em;

    private $className;

    private $relationshipMetadata;

    public function __construct(EntityManager $em, $className, RelationshipMetadata $relationshipMetadata)
    {
        $this->em = $em;
        $this->className = $className;
        $this->relationshipMetadata = $relationshipMetadata;
    }

    public function find($fromId)
    {
        $type = $this->relationshipMetadata->getType();
        $direction = $this->relationshipMetadata->getDirection();

        $identifier = sprintf('rel_%s_%s', $this->relationshipMetadata->getType(), $this->relationshipMetadata->getPropertyName());

        $statement = $this->buildStatement($fromId, $direction, $type, $identifier);
        $result = $this->em->getDatabaseDriver()->run($statement->text(), $statement->parameters());

        $repo = $this->em->getRepository($this->className);
        $instances = [];
        foreach ($result->records() as $record) {
            $instances[] = $repo->hydrate($record, true, 'end', $this->className, true);
        }

        return $instances;
    }

    public function buildStatement($fromId, $direction, $type, $identifier)
    {
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
            $query .= ' WITH end ORDER BY end.' . $this->relationshipMetadata->getOrderByPropery() . ' ' . $this->relationshipMetadata->getOrder();
        }

        $query .= ' RETURN end';

        return Statement::create($query, ['id' => $fromId]);
    }

}