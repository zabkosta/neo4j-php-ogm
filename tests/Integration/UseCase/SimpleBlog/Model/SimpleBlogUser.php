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
use GraphAware\Neo4j\OGM\Common\Collection;

/**
 * Class SimpleBlogUser.
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
