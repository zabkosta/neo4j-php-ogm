<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     *
     * @var int
     */
    protected $id;

    /**
     * @OGM\Property(type="string")
     *
     * @var string
     */
    protected $title;

    /**
     * @OGM\Property(type="string")
     *
     * @var string
     */
    protected $tagline;

    /**
     * @OGM\Property(type="int")
     *
     * @var int
     */
    protected $released;

    /**
     * @OGM\Relationship(type="ACTED_IN", direction="INCOMING", targetEntity="Person", collection=true)
     *
     * @var ArrayCollection|Person[]
     */
    protected $actors;

    /**
     * @param string      $title
     * @param string|null $released
     */
    public function __construct($title, $released = null)
    {
        $this->title = $title;
        $this->released = $released;
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
    public function getReleased()
    {
        return $this->released;
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
        if (!$this->getActors()->contains($person)) {
            $this->actors->add($person);
        }
    }

    /**
     * @param \Movies\Person $person
     */
    public function removeActor(Person $person)
    {
        if ($this->getActors()->contains($person)) {
            $this->actors->removeElement($person);
        }
    }
}
