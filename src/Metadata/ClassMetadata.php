<?php

namespace GraphAware\Neo4j\OGM\Metadata;

class ClassMetadata
{
    protected $type;

    protected $fields = [];

    protected $associations = [];

    public function __construct($type, array $fields, array $associations)
    {
        $this->type = $type;
        $this->fields = $fields;
        $this->associations = $associations;
    }

    public function getFields()
    {
        return $this->fields;
    }

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
}