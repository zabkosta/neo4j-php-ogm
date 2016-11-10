<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
