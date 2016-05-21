<?php

namespace GraphAware\Neo4j\OGM\Annotations;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class EndNode
{
    protected $targetEntity;

    public function __construct(array $values)
    {
        $this->targetEntity = $values['targetEntity'];
    }

    /**
     * @return mixed
     */
    public function getTargetEntity()
    {
        return $this->targetEntity;
    }
}
