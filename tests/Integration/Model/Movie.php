<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\Model;

use Doctrine\Common\Collections\ArrayCollection;
use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @OGM\Node(label="Movie")
 */
class Movie
{
    /**
     * @OGM\GraphId()
     */
    public $id;

    /**
     * @OGM\Property(type="string")
     */
    public $title;

    /**
     * @OGM\Relationship(targetEntity="Person", type="ACTED_IN", direction="INCOMING", collection=true)
     */
    public $actors;

    public function __construct($title = null)
    {
        if (null !== $title) {
            $this->title = $title;
        }
        $this->actors = new ArrayCollection();
    }
}