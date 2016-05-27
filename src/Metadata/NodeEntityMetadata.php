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

use GraphAware\Neo4j\OGM\Util\ClassUtils;

final class NodeEntityMetadata extends GraphEntityMetadata
{
    /**
     * @var \GraphAware\Neo4j\OGM\Metadata\NodeAnnotationMetadata
     */
    private $nodeAnnotationMetadata;

    /**
     * @var string
     */
    private $customRepository;

    /**
     * NodeEntityMetadata constructor.
     * @param string $className
     * @param \GraphAware\Neo4j\OGM\Metadata\NodeAnnotationMetadata $nodeAnnotationMetadata
     */
    public function __construct($className, \ReflectionClass $reflectionClass, NodeAnnotationMetadata $nodeAnnotationMetadata, EntityIdMetadata $entityIdMetadata, array $entityPropertiesMetadata)
    {
        parent::__construct($entityIdMetadata, $className, $reflectionClass, $entityPropertiesMetadata);
        $this->nodeAnnotationMetadata = $nodeAnnotationMetadata;
        $this->customRepository = $this->nodeAnnotationMetadata->getCustomRepository();
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

    /**
     * @return bool
     */
    public function hasCustomRepository()
    {
        return null !== $this->customRepository;
    }

    /**
     * @return string
     */
    public function getRepositoryClass()
    {
        if (null === $this->customRepository) {
            throw new \LogicException(sprintf('There is no custom repository for "%s"', $this->className));
        }

        return ClassUtils::getFullClassName($this->customRepository, $this->className);
    }

    public function getAssociatedObjects()
    {
        return array();
    }

    public function getRelationshipEntities()
    {
        return array();
    }
}