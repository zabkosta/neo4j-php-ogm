<?php

namespace GraphAware\Neo4j\OGM\Annotations;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class Relationship
{
    /**
     * @var string
     */
    public $targetEntity;

    /**
     * @var string
     */
    public $relationshipEntity;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     *
     * @Enum({"OUTGOING","INCOMING","BOTH"})
     */
    public $direction;

    /**
     * @var string
     */
    public $mappedBy;

    /**
     * @var bool
     */
    public $collection;
}
