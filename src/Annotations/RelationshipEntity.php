<?php

namespace GraphAware\Neo4j\OGM\Annotations;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class RelationshipEntity
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @Enum({"INCOMING","OUTGOING"})
     */
    public $direction;

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (!isset($values['type'])) {
            throw new \InvalidArgumentException('Missing "type" in @RelationshipEntity annotation');
        }
        $this->type = $values['type'];
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
