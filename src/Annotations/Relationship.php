<?php

namespace GraphAware\Neo4j\OGM\Annotations;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Relationship
{
    /**
     * @var string
     */
    protected $targetEntity;

    /**
     * @var string
     */
    protected $relationshipEntity;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     *
     * @Enum({"OUTGOING","INCOMING"})
     */
    public $direction;

    /**
     * @var string
     */
    protected $mappedBy;

    /**
     * @var bool
     */
    protected $collection = false;

    public function __construct(array $values)
    {
        if (isset($values['targetEntity'])) {
            $this->targetEntity = $values['targetEntity'];
        }

        if (isset($values['entity'])) {
            $this->relationshipEntity = $values['entity'];
        }

        if (isset($values['type'])) {
            $this->type = $values['type'];
        }

        if (isset($values['direction'])) {
            $this->direction = $values['direction'];
        }

        if (isset($values['collection']) && true === $values['collection']) {
            $this->collection = true;
        }

        if (isset($values['mappedBy'])) {
            $this->mappedBy = $values['mappedBy'];
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

    /**
     * @return bool
     */
    public function hasMappedBy()
    {
        return null !== $this->mappedBy;
    }

    /**
     * @return string
     */
    public function getMappedBy()
    {
        return $this->mappedBy;
    }

    /**
     * @return bool
     */
    public function isRelationshipEntity()
    {
        return null !== $this->relationshipEntity;
    }

    /**
     * @return string
     */
    public function getRelationshipEntity()
    {
        return $this->relationshipEntity;
    }
}
