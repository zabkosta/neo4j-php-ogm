<?php

namespace GraphAware\Neo4j\OGM\Persister;

use GraphAware\Common\Cypher\Statement;
use GraphAware\Neo4j\OGM\Metadata\ClassMetadata;
use GraphAware\Neo4j\OGM\Annotations\Property;
use GraphAware\Neo4j\OGM\Annotations\Label;

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
        $extraLabels = [];
        $removeLabels = [];
        $reflO = new \ReflectionObject($object);
        foreach ($this->classMetadata->getFields() as $field => $meta) {
            if ($meta instanceof Property) {
                $p = $reflO->getProperty($field);
                $p->setAccessible(true);
                $propertyValues[$field] = $p->getValue($object);
            } elseif ($meta instanceof Label) {
                $p = $reflO->getProperty($field);
                $p->setAccessible(true);
                $v = $p->getValue($object);
                if (true === $v) {
                    $extraLabels[] = $meta->name;
                } else {
                    $removeLabels[] = $meta->name;
                }
            }
        }

        $query = sprintf('CREATE (n:%s) SET n += {properties}', $this->classMetadata->getLabel());
        if (!empty($extraLabels)) {
            foreach ($extraLabels as $label) {
                $query .= ' SET n:'.$label;
            }
        }
        if (!empty($removeLabels)) {
            foreach ($removeLabels as $label) {
                $query .= ' REMOVE n:'.$label;
            }
        }

        $query .= ' RETURN id(n) as id';

        return Statement::create($query, ['properties' => $propertyValues], spl_object_hash($object));
    }

    public function getUpdateQuery($object)
    {
        $propertyValues = [];
        $reflO = new \ReflectionObject($object);
        foreach ($this->classMetadata->getFields() as $field => $meta) {
            $p = $reflO->getProperty($field);
            $p->setAccessible(true);
            $propertyValues[$field] = $p->getValue($object);
        }
        $propId = $reflO->getProperty('id');
        $propId->setAccessible(true);
        $id = $propId->getValue($object);

        foreach ($this->classMetadata->getFields() as $field => $meta) {
            if ($meta instanceof Property) {
                $p = $reflO->getProperty($field);
                $p->setAccessible(true);
                $propertyValues[$field] = $p->getValue($object);
            } elseif ($meta instanceof Label) {
                $p = $reflO->getProperty($field);
                $p->setAccessible(true);
                $v = $p->getValue($object);
                if (true === $v) {
                    $extraLabels[] = $meta->name;
                } else {
                    $removeLabels[] = $meta->name;
                }
            }
        }

        $query = 'MATCH (n) WHERE id(n) = {id} SET n += {props}';
        if (!empty($extraLabels)) {
            foreach ($extraLabels as $label) {
                $query .= ' SET n:'.$label;
            }
        }
        if (!empty($removeLabels)) {
            foreach ($removeLabels as $label) {
                $query .= ' REMOVE n:'.$label;
            }
        }

        return Statement::create($query, ['id' => $id, 'props' => $propertyValues]);
    }
}
