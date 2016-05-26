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
     * @var string
     */
    protected $className;

    /**
     * @var \ReflectionClass
     */
    protected $reflectionClass;

    public function __construct($className, \ReflectionClass $reflectionClass)
    {
        $this->className = $className;
        $this->reflectionClass = $reflectionClass;
    }

    /**
     * @var EntityPropertyMetadata[]
     */
    protected $entityPropertiesMetadata = [];

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

}