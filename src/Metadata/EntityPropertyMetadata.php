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

use GraphAware\Neo4j\OGM\Common\Collection;

class EntityPropertyMetadata
{
    /**
     * @var string
     */
    private $propertyName;

    /**
     * @var \ReflectionProperty
     */
    private $reflectionProperty;

    /**
     * @var \GraphAware\Neo4j\OGM\Metadata\PropertyAnnotationMetadata
     */
    private $propertyAnnotationMetadata;

    /**
     * @var bool
     */
    private $isAccessible;

    /**
     * EntityPropertyMetadata constructor.
     * @param string $propertyName
     * @param \GraphAware\Neo4j\OGM\Metadata\PropertyAnnotationMetadata $propertyAnnotationMetadata
     */
    public function __construct($propertyName, \ReflectionProperty $reflectionProperty, PropertyAnnotationMetadata $propertyAnnotationMetadata)
    {
        $this->propertyName = $propertyName;
        $this->reflectionProperty = $reflectionProperty;
        $this->propertyAnnotationMetadata = $propertyAnnotationMetadata;
        $this->isAccessible = $reflectionProperty->isPublic();
    }

    /**
     * @return string
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     * @param object $object
     * @param mixed $value
     */
    public function setValue($object, $value)
    {
        $this->checkAccess();
        $this->reflectionProperty->setValue($object, $value);
    }

    /**
     * @param object $object
     *
     * @return mixed
     */
    public function getValue($object)
    {
        $this->checkAccess();

        return $this->reflectionProperty->getValue($object);
    }

    /**
     *
     */
    private function checkAccess()
    {
        if (!$this->isAccessible) {
            $this->reflectionProperty->setAccessible(true);
        }
        $this->isAccessible = true;
    }
}