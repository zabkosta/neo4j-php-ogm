<?php

namespace Movies;

use Doctrine\Common\Collections\ArrayCollection;
use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @OGM\Node(label="Person")
 */
class Person
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
    protected $name;

    /**
     * @OGM\Property(type="born")
     * @var int
     */
    protected $born;

    /**
     * @OGM\Relationship(type="ACTED_IN", direction="OUTGOING", targetEntity="Movie", collection=true, mappedBy="actors")
     * @var ArrayCollection|Movie[]
     */
    protected $movies;

    /**
     * @param string $name
     * @param int|null $born
     */
    public function __construct($name, $born = null)
    {
        $this->name = $name;
        $this->born = $born;
        $this->movies = new ArrayCollection();
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
     * @return int
     */
    public function getBorn()
    {
        return $this->born;
    }

    /**
     * @param int $year
     */
    public function setBorn($year)
    {
        $this->born = $year;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\Movies\Movie[]
     */
    public function getMovies()
    {
        return $this->movies;
    }

    /**
     * @param \Movies\Movie $movie
     */
    public function addMovie(Movie $movie)
    {
        if (!$this->movies->contains($movie)) {
            $this->movies->add($movie);
        }
    }

    /**
     * @param \Movies\Movie $movie
     */
    public function removeMovie(Movie $movie)
    {
        if ($this->movies->contains($movie)) {
            $this->movies->removeElement($movie);
        }
    }
}