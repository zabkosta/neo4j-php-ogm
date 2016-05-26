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

final class NodeEntityMetadata extends GraphEntityMetadata
{
    /**
     * @var \GraphAware\Neo4j\OGM\Metadata\NodeAnnotationMetadata
     */
    private $nodeAnnotationMetadata;

    /**
     * NodeEntityMetadata constructor.
     * @param string $className
     * @param \GraphAware\Neo4j\OGM\Metadata\NodeAnnotationMetadata $nodeAnnotationMetadata
     */
    public function __construct($className, NodeAnnotationMetadata $nodeAnnotationMetadata)
    {
        $this->className = $className;
        $this->nodeAnnotationMetadata = $nodeAnnotationMetadata;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->nodeAnnotationMetadata->getLabel();
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Metadata\EntityPropertyMetadata[]
     */
    public function getPropertiesMetadata()
    {
        return $this->entityPropertiesMetadata;
    }

    /**
     * @param $key
     * @return \GraphAware\Neo4j\OGM\Metadata\EntityPropertyMetadata
     */
    public function getPropertyMetadata($key)
    {
        if (array_key_exists($key, $this->entityPropertiesMetadata)) {
            return $this->entityPropertiesMetadata[$key];
        }
    }
}