<?php

namespace Movies;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @OGM\RelationshipEntity(type="RATED")
 */
class Rating
{
    /**
     * @OGM\GraphId()
     * @var int
     */
    protected $id;

    /**
     * @OGM\StartNode(targetEntity="User")
     * @var User
     */
    protected $user;

    /**
     * @OGM\EndNode(targetEntity="Movie")
     * @var Movie
     */
    protected $movie;

    /**
     * @OGM\Property(type="float")
     * @var float
     */
    protected $score;

    /**
     * Rating constructor.
     * @param \Movies\User $user
     * @param \Movies\Movie $movie
     * @param float $score
     */
    public function __construct(User $user, Movie $movie, $score)
    {
        $this->user = $user;
        $this->movie = $movie;
        $this->score = $score;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return Movie
     */
    public function getMovie()
    {
        return $this->movie;
    }

    /**
     * @return float
     */
    public function getScore()
    {
        return $this->score;
    }
}