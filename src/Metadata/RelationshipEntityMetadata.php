<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\Metadata;

use GraphAware\Neo4j\OGM\Annotations\RelationshipEntity;

final class RelationshipEntityMetadata extends GraphEntityMetadata
{
    /**
     * @var string
     */
    private $type;

    private $relationshipEntityAnnotation;

    private $startNodeEntityMetadata;

    private $startNodeReflectionProperty;

    private $endNodeReflectionProperty;

    private $endNodeEntityMetadata;

    /**
     * RelationshipEntityMetadata constructor.
     * @param string $class
     * @param \ReflectionClass $reflectionClass
     * @param \GraphAware\Neo4j\OGM\Annotations\RelationshipEntity $annotation
     * @param \GraphAware\Neo4j\OGM\Metadata\EntityIdMetadata $entityIdMetadata
     * @param string $startNodeClass
     * @param string $endNodeClass
     * @param array $entityPropertiesMetadata
     */
    public function __construct($class, \ReflectionClass $reflectionClass, RelationshipEntity $annotation, EntityIdMetadata $entityIdMetadata, $startNodeClass, $startNodeKey, $endNodeClass, $endNodeKey, array $entityPropertiesMetadata)
    {
        parent::__construct($entityIdMetadata, $class, $reflectionClass, $entityPropertiesMetadata);
        $this->relationshipEntityAnnotation = $annotation;
        $this->startNodeEntityMetadata = $startNodeClass;
        $this->endNodeEntityMetadata = $endNodeClass;
        $this->type = $annotation->type;
        $this->startNodeReflectionProperty = $this->reflectionClass->getProperty($startNodeKey);
        $this->endNodeReflectionProperty = $this->reflectionClass->getProperty($endNodeKey);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public function getStartNode()
    {
        return $this->startNodeEntityMetadata;
    }

    public function getEndNode()
    {
        return $this->endNodeEntityMetadata;
    }

    public function setStartNodeProperty($object, $value)
    {
        $this->startNodeReflectionProperty->setAccessible(true);
        $this->startNodeReflectionProperty->setValue($object, $value);
    }

    public function getStartNodeValue($object)
    {
        $this->startNodeReflectionProperty->setAccessible(true);

        return $this->startNodeReflectionProperty->getValue($object);
    }

    public function setEndNodeProperty($object, $value)
    {
        $this->endNodeReflectionProperty->setAccessible(true);
        $this->endNodeReflectionProperty->setValue($object, $value);
    }

    public function getEndNodeProperty($object, $value)
    {
        $this->endNodeReflectionProperty->setAccessible(true);

        return $this->endNodeReflectionProperty->getValue($object);
    }
}