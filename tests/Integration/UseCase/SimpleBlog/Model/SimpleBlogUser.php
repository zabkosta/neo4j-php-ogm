<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\UseCase\SimpleBlog\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use Doctrine\Common\Collections\ArrayCollection;
use GraphAware\Neo4j\OGM\Common\Collection;

/**
 * Class SimpleBlogUser
 * @package GraphAware\Neo4j\OGM\Tests\Integration\UseCase\SimpleBlog\Model
 *
 * @OGM\Node(label="User")
 */
class SimpleBlogUser
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
    private $name;

    /**
     * @var SimpleBlogWrote[]
     *
     * @OGM\Relationship(relationshipEntity="SimpleBlogWrote", direction="OUTGOING", collection=true)
     * @OGM\Lazy()
     */
    protected $posts;

    public function __construct($name)
    {
        $this->name = $name;
        $this->posts = new Collection();
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Tests\Integration\UseCase\SimpleBlog\Model\SimpleBlogWrote[]
     */
    public function getPosts()
    {
        return $this->posts;
    }

    public function createPost($title)
    {
        $post = new SimpleBlogPost($title);
        $wroteRel = new SimpleBlogWrote($this, $post);
        $this->posts->add($wroteRel);
        $post->setPublication($wroteRel);
    }
}