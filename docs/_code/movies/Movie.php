<?php

namespace Movies;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @OGM\Node(label="Movie")
 */
class Movie
{
    /**
     * @OGM\GraphId()
     * @var int
     */
    protected $id;

    /**
     * @OGM\Property(type="string")
     * @var string
     */
    protected $title;

    /**
     * @OGM\Property(type="string")
     * @var string
     */
    protected $tagline;

    /**
     * @OGM\Property(type="int")
     * @var int
     */
    protected $release;

    /**
     * @param string $title
     * @param string|null $release
     */
    public function __construct($title, $release = null)
    {
        $this->title = $title;
        $this->release = $release;
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
     * @return string
     */
    public function getTagline()
    {
        return $this->tagline;
    }

    /**
     * @param string $tagline
     */
    public function setTagline($tagline)
    {
        $this->tagline = $tagline;
    }

    /**
     * @return int
     */
    public function getRelease()
    {
        return $this->release;
    }
}