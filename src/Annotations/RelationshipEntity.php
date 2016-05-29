<?php

namespace GraphAware\Neo4j\OGM\Annotations;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
final class RelationshipEntity implements Entity
{
    /**
     * @var string
     */
    public $type;

    /**
     * @Enum({"INCOMING","OUTGOING"})
     */
    public $direction;
}
