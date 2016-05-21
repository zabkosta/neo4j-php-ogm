<?php

namespace GraphAware\Neo4j\OGM\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Property
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
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
