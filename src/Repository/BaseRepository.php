<?php

namespace GraphAware\Neo4j\OGM\Repository;

use GraphAware\Common\Result\RecordViewInterface;
use GraphAware\Neo4j\Client\Formatter\Result;
use GraphAware\Neo4j\OGM\Manager;
use GraphAware\Neo4j\OGM\Metadata\ClassMetadata;

class BaseRepository
{
    protected $classMetadata;

    protected $manager;

    protected $className;

    public function __construct(ClassMetadata $classMetadata, Manager $manager, $className)
    {
        $this->classMetadata = $classMetadata;
        $this->manager = $manager;
        $this->className = $className;
    }

    public function findAll()
    {
        $label = $this->classMetadata->getLabel();
        $query = sprintf('MATCH (n:%s) RETURN n', $label);

        $result = $this->manager->getDatabaseDriver()->run($query);

        return $this->hydrateResultSet($result);

    }

    public function findBy($key, $value)
    {
        $label = $this->classMetadata->getLabel();
        $query = sprintf('MATCH (n:%s) WHERE n.%s = {%s} RETURN n', $label, $key, $key);

        $t = microtime(true);
        $result = $this->manager->getDatabaseDriver()->run($query, [$key => $value]);
        $t2 = microtime(true);

        echo $t2 - $t . PHP_EOL;

        return $this->hydrateResultSet($result);
    }

    public function findOneBy($key, $value)
    {
        $instances = $this->findBy($key, $value);

        if (count($instances) > 1) {
            throw new \Exception('Expected only one result, got ' . count($instances));
        }

        return isset($instances[0]) ? $instances[0] : null;
    }

    public function hydrateResultSet(Result $result)
    {
        $entities = [];
        foreach ($result->records() as $record) {
                $entities[] = $this->hydrate($record);
        }

        return $entities;
    }

    public function hydrate(RecordViewInterface $record)
    {
        $reflClass = new \ReflectionClass($this->className);
        $instance = $reflClass->newInstanceWithoutConstructor();

        foreach ($this->classMetadata->getFields() as $field => $meta) {
            if ($record->value('n')->hasValue($field)) {
                if ($property = $reflClass->getProperty($field)) {
                    $property->setAccessible(true);
                    $property->setValue($instance, $record->value('n')->value($field));
                }
            }
        }

        $property = $reflClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($instance, $record->value('n')->identity());

        return $instance;
    }
}