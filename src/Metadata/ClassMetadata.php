<?php

namespace GraphAware\Neo4j\OGM\Metadata;

class ClassMetadata
{
    protected $className;

    protected $type;

    protected $fields = [];

    protected $associations = [];

    protected $relEntities = [];

    protected $label;

    public function __construct($type, $label, array $fields, array $associations, array $relEntities)
    {
        $this->type = $type;
        $this->label = $label;
        $this->fields = $fields;
        $this->associations = $associations;
        $this->relEntities = $relEntities;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Annotations\Relationship[]
     */
    public function getAssociations()
    {
        return $this->associations;
    }

    public function addField(array $field)
    {
        $this->fields[$field[0]] = $field;
    }

    public function addAssociation(array $association)
    {
        $this->associations[$association[0]] = $association;
    }

    /**
     * @param string $key
     *
     * @return \GraphAware\Neo4j\OGM\Annotations\Relationship
     */
    public function getAssociation($key)
    {
        if (isset($this->associations[$key])) {
            return $this->associations[$key];
        }

        throw new \InvalidArgumentException(sprintf('No association with key "%s" found for class "%s"', $key, $this->className));
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Annotations\Relationship[]
     */
    public function getRelationshipEntities()
    {
        return $this->relEntities;
    }

    /**
     * @param string $key
     *
     * @return \GraphAware\Neo4j\OGM\Annotations\Relationship
     */
    public function getRelationshipEntity($key)
    {
        return $this->relEntities[$key];
    }

    public function getIdentityValue($entity)
    {
        $reflClass = new \ReflectionClass(get_class($entity));
        $property = $reflClass->getProperty('id');
        $property->setAccessible(true);

        return $property->getValue($entity);
    }

    public function getAssociatedObjects($entity)
    {
        $relatedObjects = [];
        $reflClass = new \ReflectionClass(get_class($entity));
        foreach ($this->associations as $k => $assoc) {
            $property = $reflClass->getProperty($k);
            $property->setAccessible(true);
            $value = $property->getValue($entity);
            if (null !== $value) {
                $relatedObjects[] = [$assoc, $value];
            }
        }

        return $relatedObjects;
    }
}