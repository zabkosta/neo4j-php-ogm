<?php

namespace Movies;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @OGM\Relationship(type="ACTED_IN", direction="OUTGOING", targetEntity="Person", collection=true)
     * @var ArrayCollection|Person[]
     */
    protected $actors;

    /**
     * @param string $title
     * @param string|null $release
     */
    public function __construct($title, $release = null)
    {
        $this->title = $title;
        $this->release = $release;
        $this->actors = new ArrayCollection();
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
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
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

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\Movies\Person[]
     */
    public function getActors()
    {
        return $this->actors;
    }

    /**
     * @param \Movies\Person $person
     */
    public function addActor(Person $person)
    {
        if (!$this->actors->contains($person)) {
            $this->actors->add($person);
        }
    }

    /**
     * @param \Movies\Person $person
     */
    public function removeActor(Person $person)
    {
        if ($this->actors->contains($person)) {
            $this->actors->removeElement($person);
        }
    }
}