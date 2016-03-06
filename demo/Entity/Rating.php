<?php

namespace Demo\Entity;

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
     * @OGM\StartNode(targetEntity="\Demo\Entity\User")
     * @var User
     */
    protected $user;

    /**
     * @OGM\EndNode(targetEntity="\Demo\Entity\Movie")
     * @var Movie
     */
    protected $movie;

    /**
     * @OGM\Property(type="int")
     * @var int
     */
    protected $score;

    public function __construct(User $user, Movie $movie, $score)
    {
        $this->user = $user;
        $this->movie = $movie;
        $this->score = $score;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function getMovie()
    {
        return $this->movie;
    }

    /**
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }
}