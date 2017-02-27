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
 * @OGM\Node(label="User")
 */
class User
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
    protected $login;

    /**
     * @OGM\Relationship(relationshipEntity="Rating", type="RATED", direction="OUTGOING", collection=true)
     *
     * @var Rating[]|ArrayCollection
     */
    protected $ratings;

    /**
     * @param string $login
     */
    public function __construct($login)
    {
        $this->login = $login;
        $this->ratings = new ArrayCollection();
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
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\Movies\Rating[]
     */
    public function getRatings()
    {
        return $this->ratings;
    }

    /**
     * @param \Movies\Movie $movie
     * @param float         $score
     */
    public function rateMovie(Movie $movie, $score)
    {
        $this->getRatings()->add(new Rating($this, $movie, $score));
    }
}
