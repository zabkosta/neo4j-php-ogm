<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\tests\Integration\UseCase\SimpleBlog\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * Class SimpleBlogPost.
 *
 * @OGM\Node(label="Post")
 */
class SimpleBlogPost
{
    /**
     * @var int
     *
     * @OGM\GraphId()
     */
    protected $id;

    /**
     * @var string
     *
     * @OGM\Property(type="string")
     */
    protected $title;

    /**
     * @var SimpleBlogWrote
     *
     * @OGM\Relationship(relationshipEntity="SimpleBlogWrote", direction="INCOMING", mappedBy="post", collection=false)
     * @OGM\Lazy()
     */
    private $publication;

    /**
     * @param string $title
     */
    public function __construct($title)
    {
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Tests\Integration\UseCase\SimpleBlog\Model\SimpleBlogWrote
     */
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     * @param \GraphAware\Neo4j\OGM\Tests\Integration\UseCase\SimpleBlog\Model\SimpleBlogWrote $blogWrote
     */
    public function setPublication(SimpleBlogWrote $blogWrote)
    {
        $this->publication = $blogWrote;
    }
}
