<?php

namespace GraphAware\Neo4j\OGM\Annotations;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class OrderBy
{
    /**
     * @var string
     */
    public $property;

    /**
     * @var string
     * @Enum({"ASC","DESC"})
     */
    public $order;
}