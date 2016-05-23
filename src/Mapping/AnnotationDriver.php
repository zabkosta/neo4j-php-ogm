<?php

namespace GraphAware\Neo4j\OGM\Mapping;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\FileCacheReader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use GraphAware\Neo4j\OGM\Annotations\MappedResult;
use GraphAware\Neo4j\OGM\Annotations\Node;
use GraphAware\Neo4j\OGM\Annotations\Property;
use GraphAware\Neo4j\OGM\Annotations\Relationship;
use GraphAware\Neo4j\OGM\Annotations\RelationshipEntity;
use GraphAware\Neo4j\OGM\Annotations\StartNode;
use GraphAware\Neo4j\OGM\Annotations\EndNode;
use GraphAware\Neo4j\OGM\Metadata\QueryResultMapper;
use GraphAware\Neo4j\OGM\Metadata\ResultField;
use GraphAware\Neo4j\OGM\Repository\BaseRepository;
use GraphAware\Neo4j\OGM\Annotations\QueryResult;
use GraphAware\Neo4j\OGM\Annotations\Label;

class AnnotationDriver
{
    protected $reader;

    public function __construct($cacheDirectory = null)
    {
        AnnotationRegistry::registerFile(__DIR__.'/Neo4jOGMAnnotations.php');
        $reader = new SimpleAnnotationReader();
        //$reader->addNamespace('GraphAware\Neo4j\OGM\Annotations');
        $this->reader = new FileCacheReader(
            new AnnotationReader(),
            sys_get_temp_dir(),
            $debug = true
        );
    }

    public function readQueryResult($class)
    {
        $reflClass = new \ReflectionClass($class);
        $classAnnotations = $this->reader->getClassAnnotations($reflClass);
        $isQueryResult = false;
        foreach ($classAnnotations as $classAnnotation) {
            if ($classAnnotation instanceof QueryResult) {
                $isQueryResult = true;
            }
        }
        if (!$isQueryResult) {
            throw new \RuntimeException(sprintf('The class "%s" is not a valid QueryResult entity', $class));
        }

        $queryResultMapper = new QueryResultMapper($class);

        foreach ($reflClass->getProperties() as $property) {
            foreach ($this->reader->getPropertyAnnotations($property) as $propertyAnnotation) {
                if ($propertyAnnotation instanceof MappedResult) {
                    $queryResultMapper->addField(new ResultField($property->getName(), $propertyAnnotation->type, $propertyAnnotation->target));
                }
            }
        }

        return $queryResultMapper;
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
                if ($annotation->hasCustomRepository()) {
                    $metadata['repository'] = $this->getRepositoryFullClassName($annotation->getRepositoryClass(), $class);
                }
            } elseif ($annotation instanceof RelationshipEntity) {
                $metadata['type'] = 'RelationshipEntity';
                $metadata['relType'] = $annotation->getType();
            }
        }

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
                } elseif ($propertyAnnotation instanceof Label) {
                    $metadata['fields'][$property->getName()] = $propertyAnnotation;
                }
            }
        }

        return $metadata;
    }

    /**
     * @param string $class
     * @param string $pointOfView
     *
     * @return string
     */
    private function getRepositoryFullClassName($class, $pointOfView)
    {
        $expl = explode('\\', $class);
        if (1 === count($expl)) {
            $expl2 = explode('\\', $pointOfView);
            if (1 !== count($expl2)) {
                unset($expl2[count($expl2) - 1]);
                $class = sprintf('%s\\%s', implode('\\', $expl2), $class);
            }
        }

        if (!class_exists($class)) {
            throw new \RuntimeException(sprintf('The class "%s" could not be found', $class));
        }

        $reflClass = new \ReflectionClass($class);
        if (!$reflClass->isSubclassOf(BaseRepository::class)) {
            throw new \RuntimeException(sprintf('Custom repository class "%s" must extend "%s"', $class, BaseRepository::class));
        }

        return $class;
    }
}
