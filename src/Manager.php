<?php

namespace GraphAware\Neo4j\OGM;

use GraphAware\Neo4j\OGM\Mapping\AnnotationDriver;
use GraphAware\Neo4j\Client\Client;
use GraphAware\Neo4j\OGM\Metadata\ClassMetadata;
use GraphAware\Neo4j\OGM\Metadata\RelationshipEntityMetadata;
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

    /**
     * @var \GraphAware\Neo4j\Client\Client
     */
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

    /**
     * @param string $class
     *
     * @return \GraphAware\Neo4j\OGM\Metadata\ClassMetadata
     *
     * @throws \Exception
     */
    public function getClassMetadataFor($class)
    {
        $metadata = $this->annotationDriver->readAnnotations($class);
        $metadataClass = new ClassMetadata($metadata['type'], $metadata['label'], $metadata['fields'], $metadata['associations'], $metadata['relationshipEntities']);

        return $metadataClass;
    }

    /**
     * @param string $class
     *
     * @return \GraphAware\Neo4j\OGM\Metadata\RelationshipEntityMetadata
     *
     * @throws \Exception
     */
    public function getRelationshipEntityMetadata($class)
    {
        $metadata = $this->annotationDriver->readAnnotations($class);
        $relEntityMetadata = new RelationshipEntityMetadata($metadata);

        return $relEntityMetadata;
    }

    /**
     * @param string $class
     *
     * @return \GraphAware\Neo4j\OGM\Repository\BaseRepository
     */
    public function getRepository($class)
    {
        $classMetadata = $this->getClassMetadataFor($class);
        if (!array_key_exists($class, $this->repositories)) {
            $this->repositories[$class] = new BaseRepository($classMetadata, $this, $class);
        }

        return $this->repositories[$class];
    }

    /**
     * Clear the Entity Manager
     * All entities that were managed by the unitOfWork become detached.
     */
    public function clear()
    {
        $this->uow = null;
        $this->uow = new UnitOfWork($this);
    }
}
