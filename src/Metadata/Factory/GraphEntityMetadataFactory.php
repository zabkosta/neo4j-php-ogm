<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\Metadata\Factory;

use Doctrine\Common\Annotations\Reader;
use GraphAware\Neo4j\OGM\Annotations\Label;
use GraphAware\Neo4j\OGM\Annotations\Node;
use GraphAware\Neo4j\OGM\Annotations\Relationship;
use GraphAware\Neo4j\OGM\Annotations\Lazy;
use GraphAware\Neo4j\OGM\Exception\MappingException;
use GraphAware\Neo4j\OGM\Metadata\EntityIdMetadata;
use GraphAware\Neo4j\OGM\Metadata\EntityPropertyMetadata;
use GraphAware\Neo4j\OGM\Metadata\LabeledPropertyMetadata;
use GraphAware\Neo4j\OGM\Metadata\NodeEntityMetadata;
use GraphAware\Neo4j\OGM\Metadata\RelationshipMetadata;
use GraphAware\Neo4j\OGM\Annotations\RelationshipEntity;

class GraphEntityMetadataFactory
{
    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    private $reader;

    /**
     * @var \GraphAware\Neo4j\OGM\Metadata\Factory\NodeAnnotationMetadataFactory
     */
    private $nodeAnnotationMetadataFactory;

    /**
     * @var \GraphAware\Neo4j\OGM\Metadata\Factory\PropertyAnnotationMetadataFactory
     */
    private $propertyAnnotationMetadataFactory;

    /**
     * @var \GraphAware\Neo4j\OGM\Metadata\Factory\IdAnnotationMetadataFactory
     */
    private $IdAnnotationMetadataFactory;

    /**
     * @var \GraphAware\Neo4j\OGM\Metadata\Factory\RelationshipEntityMetadataFactory
     */
    private $relationshipEntityMetadataFactory;

    /**
     * @param \Doctrine\Common\Annotations\Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
        $this->nodeAnnotationMetadataFactory = new NodeAnnotationMetadataFactory($reader);
        $this->propertyAnnotationMetadataFactory = new PropertyAnnotationMetadataFactory($reader);
        $this->IdAnnotationMetadataFactory = new IdAnnotationMetadataFactory($reader);
        $this->relationshipEntityMetadataFactory = new RelationshipEntityMetadataFactory($reader);
    }

    /**
     * @param string $className
     *
     * @return \GraphAware\Neo4j\OGM\Metadata\NodeEntityMetadata
     */
    public function create($className)
    {
        $reflectionClass = new \ReflectionClass($className);
        $entityIdMetadata = null;
        $propertiesMetadata = [];
        $relationshipsMetadata = [];

        if (null !== $annotation = $this->reader->getClassAnnotation($reflectionClass, Node::class)) {
            $annotationMetadata = $this->nodeAnnotationMetadataFactory->create($className);
            foreach ($reflectionClass->getProperties() as $reflectionProperty) {
                $propertyAnnotationMetadata = $this->propertyAnnotationMetadataFactory->create($className, $reflectionProperty->getName());
                if (null !== $propertyAnnotationMetadata) {
                    $propertiesMetadata[] = new EntityPropertyMetadata($reflectionProperty->getName(), $reflectionProperty, $propertyAnnotationMetadata);
                } else {
                    $idA = $this->IdAnnotationMetadataFactory->create($className, $reflectionProperty);
                    if (null !== $idA) {
                        $entityIdMetadata = new EntityIdMetadata($reflectionProperty->getName(), $reflectionProperty, $idA);
                    }
                }
                foreach ($this->reader->getPropertyAnnotations($reflectionProperty) as $annot) {
                    if ($annot instanceof Label) {
                        $propertiesMetadata[] = new LabeledPropertyMetadata($reflectionProperty->getName(), $reflectionProperty, $annot);
                    }

                    if ($annot instanceof Relationship) {
                        $isLazy = null !== $this->reader->getPropertyAnnotation($reflectionProperty, Lazy::class);
                        $relationshipsMetadata[] = new RelationshipMetadata($className, $reflectionProperty, $annot, $isLazy);
                    }
                }
            }

            return new NodeEntityMetadata($className, $reflectionClass, $annotationMetadata, $entityIdMetadata, $propertiesMetadata, $relationshipsMetadata);
        } elseif (null !== $annotation = $this->reader->getClassAnnotation($reflectionClass, RelationshipEntity::class)) {
            return $this->relationshipEntityMetadataFactory->create($className);
        }

        throw new MappingException(sprintf('The class "%s" is not a valid OGM entity', $className));
    }
}
