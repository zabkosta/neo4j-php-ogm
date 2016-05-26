<?php

namespace GraphAware\Neo4j\OGM\Annotations;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
final class Node implements Entity
{
    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    protected $repository;
}
