<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\Tests\Integration\UseCase\SimpleBlog\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * Class SimpleBlogWrote.
 *
 * @OGM\RelationshipEntity(type="WROTE")
 */
class SimpleBlogWrote
{
    /**
     * @var int
     *
     * @OGM\GraphId()
     */
    protected $id;

    /**
     * @var SimpleBlogUser
     *
     * @OGM\StartNode(targetEntity="SimpleBlogUser")
     */
    private $user;

    /**
     * @var SimpleBlogPost
     *
     * @OGM\EndNode(targetEntity="SimpleBlogPost")
     */
    protected $post;

    /**
     * @var int
     *
     * @OGM\Property(type="int")
     */
    protected $timestamp;

    public function __construct(SimpleBlogUser $user, SimpleBlogPost $post)
    {
        $this->user = $user;
        $this->post = $post;
        $this->timestamp = time();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Tests\Integration\UseCase\SimpleBlog\Model\SimpleBlogUser
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Tests\Integration\UseCase\SimpleBlog\Model\SimpleBlogPost
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
