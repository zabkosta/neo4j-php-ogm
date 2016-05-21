<?php

namespace GraphAware\Neo4j\OGM\Mapping;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\FileCacheReader;
use GraphAware\Neo4j\OGM\Annotations\Node;
use GraphAware\Neo4j\OGM\Annotations\Property;
use GraphAware\Neo4j\OGM\Annotations\Relationship;
use GraphAware\Neo4j\OGM\Annotations\RelationshipEntity;
use GraphAware\Neo4j\OGM\Annotations\StartNode;
use GraphAware\Neo4j\OGM\Annotations\EndNode;

class AnnotationDriver
{
    protected $reader;

    public function __construct($cacheDirectory = null)
    {
        $this->reader = new FileCacheReader(
            new AnnotationReader(),
            sys_get_temp_dir(),
            $debug = true
        );
    }

    public function readAnnotations($class)
    {
        $reflClass = new \ReflectionClass($class);
        $classAnnotations = $this->reader->getClassAnnotations($reflClass);
        $metadata = [
            'fields' => [],
            'associations' => [],
            'relationshipEntities' => [],
        ];

        foreach ($classAnnotations as $annotation) {
            if ($annotation instanceof Node) {
                $metadata['type'] = 'Node';
                $metadata['label'] = $annotation->getLabel();
            } elseif ($annotation instanceof RelationshipEntity) {
                $metadata['type'] = 'RelationshipEntity';
                $metadata['relType'] = $annotation->getType();
            }
        }
        print_r($metadata);

        if (!array_key_exists('type', $metadata)) {
            throw new \Exception(sprintf('The class %s is not a valid OGM entity', $class));
        }

        foreach ($reflClass->getProperties() as $property) {
            foreach ($this->reader->getPropertyAnnotations($property) as $propertyAnnotation) {
                if ($propertyAnnotation instanceof Property) {
                    $metadata['fields'][$property->getName()] = $propertyAnnotation;
                } elseif ($propertyAnnotation instanceof Relationship && !$propertyAnnotation->isRelationshipEntity()) {
                    $metadata['associations'][$property->getName()] = $propertyAnnotation;
                } elseif ($propertyAnnotation instanceof Relationship && $propertyAnnotation->isRelationshipEntity()) {
                    $metadata['relationshipEntities'][$property->getName()] = $propertyAnnotation;
                } elseif ($propertyAnnotation instanceof StartNode) {
                    $metadata['start_node'] = $propertyAnnotation;
                    $metadata['start_node_key'] = $property->getName();
                } elseif ($propertyAnnotation instanceof EndNode) {
                    $metadata['end_node'] = $propertyAnnotation;
                    $metadata['end_node_key'] = $property->getName();
                }
            }
        }

        return $metadata;
    }
}
