<?php

namespace GraphAware\Neo4j\OGM;

use GraphAware\Neo4j\OGM\Mapping\AnnotationDriver;
use GraphAware\Neo4j\Client\Client;
use GraphAware\Neo4j\OGM\Metadata\ClassMetadata;
use GraphAware\Neo4j\OGM\Repository\BaseRepository;

class Manager
{
    /**
     * @var \GraphAware\Neo4j\OGM\Mapping\AnnotationDriver
     */
    protected $annotationDriver;

    /**
     * @var \GraphAware\Neo4j\OGM\UnitOfWork
     */
    protected $uow;


    protected $databaseDriver;

    protected $repositories = [];

    public function __construct(Client $databaseDriver)
    {
        $this->annotationDriver = new AnnotationDriver();
        $this->uow = new UnitOfWork($this);
        $this->databaseDriver = $databaseDriver;
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Mapping\AnnotationDriver
     */
    public function getAnnotationDriver()
    {
        return $this->annotationDriver;
    }

    public function save($entity)
    {
        if (!is_object($entity)) {
            throw new \Exception('Entity is not an object');
        }
    }

    public function persist($entity)
    {
        if (!is_object($entity)) {
            throw new \Exception('Manager::persist() expects an object');
        }

        $this->uow->persist($entity);
    }

    public function flush()
    {
        $this->uow->flush();
    }

    /**
     * @return \GraphAware\Neo4j\OGM\UnitOfWork
     */
    public function getUnitOfWork()
    {
        return $this->uow;
    }

    /**
     * @return \GraphAware\Neo4j\Client\Client
     */
    public function getDatabaseDriver()
    {
        return $this->databaseDriver;
    }

    public function getClassMetadataFor($class)
    {
        $metadata = $this->annotationDriver->readAnnotations($class);
        $metadataClass = new ClassMetadata($metadata['type'], $metadata['label'], $metadata['fields'], $metadata['associations']);

        return $metadataClass;
    }

    public function getRepository($class)
    {
        $classMetadata = $this->getClassMetadataFor($class);
        if (!array_key_exists($class, $this->repositories)) {
            $this->repositories[$class] = new BaseRepository($classMetadata, $this, $class);
        }

        return $this->repositories[$class];
    }
}