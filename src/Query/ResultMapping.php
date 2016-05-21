<?php

namespace GraphAware\Neo4j\OGM\Query;

class ResultMapping
{
    /**
     * @var string
     */
    protected $rootEntity;

    /**
     * @var string
     */
    protected $rootIdentifier;

    /**
     * @var array
     */
    protected $mappings = [];

    /**
     * @param string $rootEntity
     * @param string $rootIdentifier
     *
     * @return \GraphAware\Neo4j\OGM\Query\ResultMapping
     */
    public static function build($rootEntity, $rootIdentifier)
    {
        return new self($rootEntity, $rootIdentifier);
    }

    /**
     * @param string $rootEntity
     * @param string $rootIdentifier
     */
    public function __construct($rootEntity, $rootIdentifier)
    {
        if (!class_exists($rootEntity)) {
            throw new \RuntimeException(sprintf('The class "%s" could not be found', $rootEntity));
        }
        $this->rootEntity = $rootEntity;
        $this->rootIdentifier = $rootIdentifier;
    }

    /**
     * @param string $identifier
     * @param string $class
     *
     * @return $this
     */
    public function addMapping($identifier, $class, $property)
    {
        if (!class_exists($class)) {
            throw new \RuntimeException(sprintf('The class "%s" could not be found', $class));
        }
        $this->mappings[$identifier] = [$class, $property];

        return $this;
    }

    /**
     * @return string
     */
    public function getRootEntity()
    {
        return $this->rootEntity;
    }

    /**
     * @return string
     */
    public function getRootIdentifier()
    {
        return $this->rootIdentifier;
    }

    /**
     * @return array
     */
    public function getMappings()
    {
        return $this->mappings;
    }
}