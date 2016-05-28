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
     * @var LabeledPropertyMetadata[]
     */
    protected $labeledPropertiesMetadata = [];

    /**
     * @var RelationshipMetadata[]
     */
    protected $relationships = [];

    /**
     * NodeEntityMetadata constructor.
     *
     * @param \GraphAware\Neo4j\OGM\Metadata\EntityIdMetadata       $className
     * @param \ReflectionClass                                      $reflectionClass
     * @param \GraphAware\Neo4j\OGM\Metadata\NodeAnnotationMetadata $nodeAnnotationMetadata
     * @param \GraphAware\Neo4j\OGM\Metadata\EntityIdMetadata       $entityIdMetadata
     * @param array                                                 $entityPropertiesMetadata
     * @param RelationshipMetadata[]                                $simpleRelationshipsMetadata
     */
    public function __construct(
        $className,
        \ReflectionClass $reflectionClass,
        NodeAnnotationMetadata $nodeAnnotationMetadata,
        EntityIdMetadata $entityIdMetadata,
        array $entityPropertiesMetadata,
        array $simpleRelationshipsMetadata
    ) {
        parent::__construct($entityIdMetadata, $className, $reflectionClass, $entityPropertiesMetadata);
        $this->nodeAnnotationMetadata = $nodeAnnotationMetadata;
        $this->customRepository = $this->nodeAnnotationMetadata->getCustomRepository();
        foreach ($entityPropertiesMetadata as $o) {
            if ($o instanceof LabeledPropertyMetadata) {
                $this->labeledPropertiesMetadata[$o->getPropertyName()] = $o;
            }
        }
        foreach ($simpleRelationshipsMetadata as $relationshipMetadata) {
            $this->relationships[$relationshipMetadata->getPropertyName()] = $relationshipMetadata;
        }
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->nodeAnnotationMetadata->getLabel();
    }

    /**
     * @param $key
     *
     * @return \GraphAware\Neo4j\OGM\Metadata\LabeledPropertyMetadata
     */
    public function getLabeledProperty($key)
    {
        if (array_key_exists($key, $this->labeledPropertiesMetadata)) {
            return $this->labeledPropertiesMetadata[$key];
        }
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Metadata\LabeledPropertyMetadata[]
     */
    public function getLabeledProperties()
    {
        return $this->labeledPropertiesMetadata;
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

    /**
     * @return \GraphAware\Neo4j\OGM\Metadata\RelationshipMetadata[]
     */
    public function getRelationships()
    {
        return $this->relationships;
    }

    /**
     * @param $key
     *
     * @return \GraphAware\Neo4j\OGM\Metadata\RelationshipMetadata
     */
    public function getRelationship($key)
    {
        if (array_key_exists($key, $this->relationships)) {
            return $this->relationships[$key];
        }
    }

    /**
     * @return RelationshipMetadata[]
     */
    public function getSimpleRelationships()
    {
        $coll = [];
        foreach ($this->relationships as $relationship) {
            if (!$relationship->isRelationshipEntity()) {
                $coll[] = $relationship;
            }
        }

        return $coll;
    }

    /**
     * @return RelationshipMetadata[]|RelationshipEntityMetadata[]
     */
    public function getRelationshipEntities()
    {
        $coll = [];
        foreach ($this->relationships as $relationship) {
            if ($relationship->isRelationshipEntity()) {
                $coll[] = $relationship;
            }
        }

        return $coll;
    }

    /**
     * @return array
     */
    public function getAssociatedObjects()
    {
        return $this->getSimpleRelationships();
    }
}
