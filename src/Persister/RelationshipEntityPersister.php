<?php

namespace GraphAware\Neo4j\OGM\Persister;

use GraphAware\Common\Cypher\Statement;
use GraphAware\Neo4j\OGM\Manager;
use GraphAware\Neo4j\OGM\Metadata\RelationshipEntityMetadata;
use GraphAware\Neo4j\OGM\Util\ClassUtils;

class RelationshipEntityPersister
{
    protected $em;

    protected $class;

    /**
     * @var \GraphAware\Neo4j\OGM\Metadata\RelationshipEntityMetadata
     */
    protected $classMetadata;

    public function __construct(Manager $manager, $className, RelationshipEntityMetadata $classMetadata)
    {
        $this->em = $manager;
        $this->class = $className;
        $this->classMetadata = $classMetadata;
    }

    public function getCreateQuery($entity, $pov)
    {
        $class = ClassUtils::getFullClassName(get_class($entity), $pov);
        $relationshipEntityMetadata = $this->em->getRelationshipEntityMetadata($class);
        $startNode = $relationshipEntityMetadata->getStartNodeValue($entity);
        $startNodeId = $this->em->getClassMetadataFor(get_class($startNode))->getIdValue($startNode);
        $endNode = $relationshipEntityMetadata->getEndNodeValue($entity);
        $endNodeId = $this->em->getClassMetadataFor(get_class($endNode))->getIdValue($endNode);

        $relType = $this->classMetadata->getType();

        $query = 'MATCH (a), (b) WHERE id(a) = {a} AND id(b) = {b}'.PHP_EOL;
        $query .= sprintf('MERGE (a)-[r:%s]->(b) SET r += {fields}', $relType).PHP_EOL;
        $query .= 'RETURN id(r)';

        $parameters = [
            'a' => $startNodeId,
            'b' => $endNodeId,
            'fields' => [],
        ];

        foreach ($this->classMetadata->getPropertiesMetadata() as $propertyMetadata) {
            $v = $propertyMetadata->getValue($entity);
            $parameters['fields'][$propertyMetadata->getPropertyName()] = $v;
        }

        return Statement::create($query, $parameters);
    }

    public function getUpdateQuery($entity)
    {
        $id = $this->classMetadata->getIdValue($entity);

        $query = sprintf('START rel=rel(%d) SET rel += {fields}', $id);

        $parameters = [
            'fields' => [],
        ];

        foreach ($this->classMetadata->getPropertiesMetadata() as $propertyMetadata) {
            $v = $propertyMetadata->getValue($entity);
            $parameters['fields'][$propertyMetadata->getPropertyName()] = $v;
        }

        return Statement::create($query, $parameters);
    }

    public function getDeleteQuery($entity)
    {
        $id = $this->classMetadata->getIdValue($entity);
        $query = 'START rel=rel('.$id.') DELETE rel';

        return Statement::create($query);
    }
}
