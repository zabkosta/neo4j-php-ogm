<?php

namespace GraphAware\Neo4j\OGM\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class MappedResult
{
    /**
     * @var string
     *
     * @Enum({"ENTITY","STRING","BOOLEAN","ARRAY","FLOAT","INTEGER"})
     */
    public $type;

    /**
     * @var string
     */
    public $target;
}