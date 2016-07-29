<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\UseCase\SimpleBlog\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * Class SimpleBlogPost
 * @package GraphAware\Neo4j\OGM\Tests\Integration\UseCase\SimpleBlog\Model
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