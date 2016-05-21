<?php

namespace GraphAware\Neo4j\OGM\Annotations;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Node
{
    /**
     * @var string
     */
    protected $label;

    public function __construct(array $values)
    {
        $this->label = $values['label'];
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }
}
