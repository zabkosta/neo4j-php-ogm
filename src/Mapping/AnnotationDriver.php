<?php

namespace GraphAware\Neo4j\OGM\Mapping;

use Doctrine\Common\Annotations\AnnotationReader;
use GraphAware\Neo4j\OGM\Annotations\Node;
use GraphAware\Neo4j\OGM\Annotations\Property;
use GraphAware\Neo4j\OGM\Annotations\RelatedNode;

class AnnotationDriver
{
    protected $reader;

    public function __construct()
    {
        $this->reader = new AnnotationReader();
    }

    public function readAnnotations($class)
    {
        $reflClass = new \ReflectionClass($class);
        $classAnnotations = $this->reader->getClassAnnotations($reflClass);
        $metadata = [
            'fields' => [],
            'associations' => []
        ];

        foreach ($classAnnotations as $annotation) {
            if ($annotation instanceof Node) {
                $metadata['type'] = 'Node';
                $metadata['label'] = $annotation->getLabel();
            }
        }

        if (!array_key_exists('type', $metadata)) {
            throw new \Exception(sprintf('The class %s is not a valid OGM entity', $class));
        }

        foreach ($reflClass->getProperties() as $property) {
            foreach ($this->reader->getPropertyAnnotations($property) as $propertyAnnotation) {
                if ($propertyAnnotation instanceof Property) {
                    $metadata['fields'][$property->getName()] = $propertyAnnotation;
                } elseif ($propertyAnnotation instanceof RelatedNode) {
                    $metadata['associations'][] = $propertyAnnotation;
                }
            }
        }

        return $metadata;
    }
}