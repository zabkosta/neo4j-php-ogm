<?php

namespace GraphAware\Neo4j\OGM\Persister;

use GraphAware\Common\Cypher\Statement;
use GraphAware\Neo4j\OGM\Manager;
use GraphAware\Neo4j\OGM\Metadata\RelationshipEntityMetadata;

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

    public function getCreateQuery($entity)
    {
        $reflClass = new \ReflectionClass(get_class($entity));
        $startNodeProperty = $reflClass->getProperty($this->classMetadata->getStartNodeKey());
        $startNodeProperty->setAccessible(true);
        $startNode = $startNodeProperty->getValue($entity);
        $startNodeId = $this->em->getClassMetadataFor($this->classMetadata->getStartNode()->getTargetEntity())->getIdentityValue($startNode);

        $endNodeProperty = $reflClass->getProperty($this->classMetadata->getEndNodeKey());
        $endNodeProperty->setAccessible(true);
        $endNode = $endNodeProperty->getValue($entity);
        $endNodeId = $this->em->getClassMetadataFor($this->classMetadata->getEndNode()->getTargetEntity())->getIdentityValue($endNode);

        $relType = $this->classMetadata->getType();

        $query = 'MATCH (a), (b) WHERE id(a) = {a} AND id(b) = {b}'.PHP_EOL;
        $query .= sprintf('MERGE (a)-[r:%s]->(b) SET r += {fields}', $relType).PHP_EOL;
        $query .= 'RETURN id(r)';

        $parameters = [
            'a' => $startNodeId,
            'b' => $endNodeId,
            'fields' => [],
        ];

        foreach ($this->classMetadata->getFields() as $field => $annot) {
            $prop = $reflClass->getProperty($field);
            $prop->setAccessible(true);
            $v = $prop->getValue($entity);
            $parameters['fields'][$field] = $v;
        }

        return Statement::create($query, $parameters);
    }

    public function getUpdateQuery($entity)
    {
        $reflClass = new \ReflectionClass(get_class($entity));
        $id = $this->classMetadata->getObjectInternalId($entity);

        $query = sprintf('START rel=rel(%d) SET rel += {fields}', $id);

        $parameters = [
            'fields' => [],
        ];

        foreach ($this->classMetadata->getFields() as $field => $annot) {
            $prop = $reflClass->getProperty($field);
            $prop->setAccessible(true);
            $v = $prop->getValue($entity);
            $parameters['fields'][$field] = $v;
        }

        return Statement::create($query, $parameters);
    }

    public function getDeleteQuery($entity)
    {
        $id = $this->classMetadata->getObjectInternalId($entity);
        $query = 'START rel=rel('.$id.') DELETE rel';

        return Statement::create($query);
    }
}
