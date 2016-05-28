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

use GraphAware\Neo4j\OGM\Annotations\Relationship;
use GraphAware\Neo4j\OGM\Common\Collection;
use GraphAware\Neo4j\OGM\Util\ClassUtils;

final class RelationshipMetadata
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $propertyName;

    /**
     * @var \ReflectionProperty
     */
    private $reflectionProperty;

    /**
     * @var \GraphAware\Neo4j\OGM\Annotations\Relationship
     */
    private $relationshipAnnotation;

    /**
     * @param string                                         $className
     * @param \ReflectionProperty                            $reflectionProperty
     * @param \GraphAware\Neo4j\OGM\Annotations\Relationship $relationshipAnnotation
     */
    public function __construct($className, \ReflectionProperty $reflectionProperty, Relationship $relationshipAnnotation)
    {
        $this->className = $className;
        $this->propertyName = $reflectionProperty->getName();
        $this->reflectionProperty = $reflectionProperty;
        $this->relationshipAnnotation = $relationshipAnnotation;
    }

    /**
     * @return string
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->relationshipAnnotation->type;
    }

    /**
     * @return bool
     */
    public function isRelationshipEntity()
    {
        return null !== $this->relationshipAnnotation->relationshipEntity;
    }

    /**
     * @return bool
     */
    public function isCollection()
    {
        return true === $this->relationshipAnnotation->collection;
    }

    /**
     * @return string
     */
    public function getDirection()
    {
        return $this->relationshipAnnotation->direction;
    }

    /**
     * @return string
     */
    public function getTargetEntity()
    {
        return ClassUtils::getFullClassName($this->relationshipAnnotation->targetEntity, $this->className);
    }

    /**
     * @return string
     */
    public function getRelationshipEntityClass()
    {
        return ClassUtils::getFullClassName($this->relationshipAnnotation->relationshipEntity, $this->className);
    }

    /**
     * @return bool
     */
    public function hasMappedByProperty()
    {
        return null !== $this->relationshipAnnotation->mappedBy;
    }

    /**
     * @return string
     */
    public function getMappedByProperty()
    {
        return $this->relationshipAnnotation->mappedBy;
    }

    /**
     * @param $object
     */
    public function initializeCollection($object)
    {
        if (!$this->isCollection()) {
            throw new \LogicException(sprintf('The property mapping this relationship is not of collection type in "%s"', $this->className));
        }

        $this->setValue($object, new Collection());
    }

    /**
     * @param object $object
     * @param mixed  $value
     */
    public function addToCollection($object, $value)
    {
        if (!$this->isCollection()) {
            throw new \LogicException(sprintf('The property mapping this relationship is not of collection type in "%s"', $this->className));
        }

        /** @var Collection $coll */
        $coll = $this->getValue($object);
        $coll->add($value);
    }

    /**
     * @param $object
     *
     * @return mixed
     */
    public function getValue($object)
    {
        $this->reflectionProperty->setAccessible(true);

        return $this->reflectionProperty->getValue($object);
    }

    /**
     * @param $object
     * @param $value
     */
    public function setValue($object, $value)
    {
        $this->reflectionProperty->setAccessible(true);
        $this->reflectionProperty->setValue($object, $value);
    }
}
