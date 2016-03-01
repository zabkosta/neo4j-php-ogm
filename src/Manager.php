<?php

namespace GraphAware\Neo4j\OGM;

use GraphAware\Neo4j\OGM\Mapping\AnnotationDriver;
use GraphAware\Neo4j\Client\Client;
use GraphAware\Neo4j\OGM\Metadata\ClassMetadata;

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
        $metadataClass = new ClassMetadata($metadata['type'], $metadata['fields'], $metadata['associations']);

        return $metadataClass;
    }
}