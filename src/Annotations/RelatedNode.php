<?php

namespace GraphAware\Neo4j\OGM\Annotations;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class RelatedNode
{
    /**
     * @var string
     */
    protected $targetEntity;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $direction;

    /**
     * @var bool
     */
    protected $collection = false;

    public function __construct(array $values)
    {
        $this->targetEntity = $values['targetEntity'];
        $this->type = $values['type'];
        $this->direction = $values['direction'];
        if (isset($values['collection']) && true === $values['collection']) {
            $this->collection = true;
        }
    }

    /**
     * @return string
     */
    public function getTargetEntity()
    {
        return $this->targetEntity;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @return mixed
     */
    public function getCollection()
    {
        return $this->collection;
    }
}