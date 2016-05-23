<?php

namespace GraphAware\Neo4j\OGM\Metadata;

class QueryResultMapper
{
    protected $className;

    /**
     * @var ResultField[]
     */
    protected $fields = [];

    public function __construct($className)
    {
        $this->className = $className;
    }

    public function addField(ResultField $field)
    {
        $this->fields[] = $field;
    }

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return ResultField[]
     */
    public function getFields()
    {
        return $this->fields;
    }
}
