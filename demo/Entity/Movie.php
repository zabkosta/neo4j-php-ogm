<?php

namespace Demo\Entity;

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
    protected $id;

    /**
     * @OGM\Property(type="string")
     */
    protected $title;

    /**
     * @OGM\Relationship(entity="Demo\Entity\Rating", collection=true)
     */
    protected $ratings;

    public function __construct($title)
    {
        $this->title = $title;
        $this->ratings = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    public function setTile($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getRatings()
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating)
    {
        $this->ratings->add($rating);
    }
}