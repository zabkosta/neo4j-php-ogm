<?php

namespace GraphAware\Neo4j\OGM\Annotations;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Lazy
{
    /**
     * @var string
     */
    public $method;
}
