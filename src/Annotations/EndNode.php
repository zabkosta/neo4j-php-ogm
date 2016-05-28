<?php

namespace GraphAware\Neo4j\OGM\Annotations;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class EndNode
{
    /**
     * @var string
     */
    public $targetEntity;
}
