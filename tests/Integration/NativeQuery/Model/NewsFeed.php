<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\NativeQuery\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @OGM\QueryResult()
 */
class NewsFeed
{
    /**
     * @OGM\MappedResult(type="ENTITY", target="Post")
     */
    protected $post;

    /**
     * @OGM\MappedResult(type="INTEGER")
     */
    protected $timestamp;

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
    public function getTimestamp()
    {
        return $this->timestamp;
    }


}