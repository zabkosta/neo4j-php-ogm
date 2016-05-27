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

abstract class GraphEntityMetadata
{
    /**
     * @var \GraphAware\Neo4j\OGM\Metadata\EntityIdMetadata
     */
    protected $entityIdMetadata;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var \ReflectionClass
     */
    protected $reflectionClass;

    /**
     * @var EntityPropertyMetadata[]
     */
    protected $entityPropertiesMetadata = [];

    /**
     * GraphEntityMetadata constructor.
     * @param \GraphAware\Neo4j\OGM\Metadata\EntityIdMetadata $entityIdMetadata
     * @param string $className
     * @param \ReflectionClass $reflectionClass
     * @param EntityPropertyMetadata[] $entityPropertiesMetadata
     */
    public function __construct(EntityIdMetadata $entityIdMetadata, $className, \ReflectionClass $reflectionClass, array $entityPropertiesMetadata)
    {
        $this->entityIdMetadata = $entityIdMetadata;
        $this->className = $className;
        $this->reflectionClass = $reflectionClass;
        foreach ($entityPropertiesMetadata as $meta) {
            $this->entityPropertiesMetadata[$meta->getPropertyName()] = $meta;
        }
    }

    /**
     * @param \GraphAware\Neo4j\OGM\Metadata\EntityPropertyMetadata $entityPropertyMetadata
     */
    public function addPropertyMetadata(EntityPropertyMetadata $entityPropertyMetadata)
    {
        $this->entityPropertiesMetadata[$entityPropertyMetadata->getPropertyName()] = $entityPropertyMetadata;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return object
     */
    public function newInstance()
    {
        return $this->reflectionClass->newInstanceWithoutConstructor();
    }

    /**
     * @param $object
     * @return mixed
     */
    public function getIdValue($object)
    {
        return $this->entityIdMetadata->getValue($object);
    }

    /**
     * @param $object
     * @param $value
     */
    public function setId($object, $value)
    {
        $this->entityIdMetadata->setValue($object, $value);
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->entityIdMetadata->getPropertyName();
    }

}