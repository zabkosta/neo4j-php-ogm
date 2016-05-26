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

class EntityPropertyMetadata
{
    /**
     * @var string
     */
    private $propertyName;

    /**
     * @var \GraphAware\Neo4j\OGM\Metadata\PropertyAnnotationMetadata
     */
    private $propertyAnnotationMetadata;

    /**
     * EntityPropertyMetadata constructor.
     * @param string $propertyName
     * @param \GraphAware\Neo4j\OGM\Metadata\PropertyAnnotationMetadata $propertyAnnotationMetadata
     */
    public function __construct($propertyName, PropertyAnnotationMetadata $propertyAnnotationMetadata)
    {
        $this->propertyName = $propertyName;
        $this->propertyAnnotationMetadata = $propertyAnnotationMetadata;
    }

    /**
     * @return string
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }
}