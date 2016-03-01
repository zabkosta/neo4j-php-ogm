<?php

namespace GraphAware\Neo4j\OGM\Persister;

use GraphAware\Common\Cypher\Statement;
use GraphAware\Neo4j\OGM\Metadata\ClassMetadata;

class EntityPersister
{
    protected $classMetadata;

    protected $className;

    public function __construct($className, ClassMetadata $classMetadata)
    {
        $this->className = $className;
        $this->classMetadata = $classMetadata;
    }

    public function getCreateQuery($object)
    {
        $propertyValues = [];
        $reflO = new \ReflectionObject($object);
        foreach ($this->classMetadata->getFields() as $field => $meta) {
            $p = $reflO->getProperty($field);
            $p->setAccessible(true);
            $propertyValues[$field] = $p->getValue($object);
        }

        $query = sprintf('CREATE (n:%s) SET n += {properties} RETURN id(n) as id', $this->classMetadata->getLabel());

        return Statement::create($query, ['properties' => $propertyValues], spl_object_hash($object));
    }
}