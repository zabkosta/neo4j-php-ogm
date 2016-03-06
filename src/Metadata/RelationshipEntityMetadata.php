<?php

namespace GraphAware\Neo4j\OGM\Metadata;

class RelationshipEntityMetadata
{
    protected $type;

    /**
     * @var \GraphAware\Neo4j\OGM\Annotations\StartNode
     */
    protected $startNode;

    protected $startNodeKey;

    /**
     * @var \GraphAware\Neo4j\OGM\Annotations\EndNode
     */
    protected $endNode;

    protected $endNodeKey;

    protected $fields;

    public function __construct(array $metadata)
    {
        $this->type = $metadata['relType'];
        $this->startNode = $metadata['start_node'];
        $this->startNodeKey = $metadata['start_node_key'];
        $this->endNode = $metadata['end_node'];
        $this->endNodeKey = $metadata['end_node_key'];
        $this->fields = $metadata['fields'];
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Annotations\StartNode
     */
    public function getStartNode()
    {
        return $this->startNode;
    }

    /**
     * @return mixed
     */
    public function getStartNodeKey()
    {
        return $this->startNodeKey;
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Annotations\EndNode
     */
    public function getEndNode()
    {
        return $this->endNode;
    }

    /**
     * @return mixed
     */
    public function getEndNodeKey()
    {
        return $this->endNodeKey;
    }

    /**
     * @return mixed
     */
    public function getFields()
    {
        return $this->fields;
    }
}