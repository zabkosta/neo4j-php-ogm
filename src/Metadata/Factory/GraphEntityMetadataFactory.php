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
use GraphAware\Neo4j\OGM\Annotations\Node;
use GraphAware\Neo4j\OGM\Exception\MappingException;
use GraphAware\Neo4j\OGM\Metadata\EntityPropertyMetadata;
use GraphAware\Neo4j\OGM\Metadata\NodeEntityMetadata;

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
     * @param \Doctrine\Common\Annotations\Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
        $this->nodeAnnotationMetadataFactory = new NodeAnnotationMetadataFactory($reader);
        $this->propertyAnnotationMetadataFactory = new PropertyAnnotationMetadataFactory($reader);
    }

    /**
     * @param string $className
     * @return \GraphAware\Neo4j\OGM\Metadata\NodeEntityMetadata
     */
    public function create($className)
    {
        $reflectionClass = new \ReflectionClass($className);

        if (null !== $annotation = $this->reader->getClassAnnotation($reflectionClass, Node::class)) {
            $entityMetadata = new NodeEntityMetadata($className, $this->nodeAnnotationMetadataFactory->create($className));
            foreach ($reflectionClass->getProperties() as $reflectionProperty) {
                $propertyAnnotationMetadata = $this->propertyAnnotationMetadataFactory->create($className, $reflectionProperty->getName());
                if (null !== $propertyAnnotationMetadata) {
                    $entityMetadata->addPropertyMetadata(new EntityPropertyMetadata($reflectionProperty->getName(), $propertyAnnotationMetadata));
                }
            }

            return $entityMetadata;
        }

        throw new MappingException(sprintf('The class "%s" is not a valid OGM entity'));
    }
}