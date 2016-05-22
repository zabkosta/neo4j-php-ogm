<?php

namespace GraphAware\Neo4j\OGM\Tests\Mapping;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @OGM\QueryResult()
 */
class QueryResultEntity
{
    /**
     * @OGM\MappedResult(type="entity", target="Post")
     */
    protected $post;

    /**
     * @OGM\MappedResult(type="entity", target="User")
     */
    protected $author;

    /**
     * @OGM\MappedResult(type="boolean")
     */
    protected $isCurrentUserOwner;

    /**
     * @return mixed
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return mixed
     */
    public function getIsCurrentUserOwner()
    {
        return $this->isCurrentUserOwner;
    }

}