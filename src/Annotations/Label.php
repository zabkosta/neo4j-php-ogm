<?php

namespace GraphAware\Neo4j\OGM\Annotations;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class Label
{
    /**
     * @var string
     */
    public $name;
}