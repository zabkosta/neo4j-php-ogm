<?php

namespace GraphAware\Neo4j\OGM\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class Property
{
    /**
     * @var string
     * @Enum({"string","boolean","array","int","float"})
     */
    public $type;

    /**
     * @var string
     */
    public $key;

    /*
     * @var bool
     */
    public $nullable;
}
