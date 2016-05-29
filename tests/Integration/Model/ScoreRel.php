<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * Class ScoreRel
 * @package GraphAware\Neo4j\OGM\Tests\Integration\Model
 *
 * @OGM\RelationshipEntity(type="HAS_SCORE")
 */
class ScoreRel
{
    /**
     * @OGM\GraphId()
     */
    protected $id;

    /**
     * @OGM\StartNode(targetEntity="Movie")
     */
    protected $movie;

    /**
     * @OGM\EndNode(targetEntity="Score")
     */
    protected $score;

    /**
     * @OGM\Property(type="float")
     */
    protected $finalScore;

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
    public function getMovie()
    {
        return $this->movie;
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @return mixed
     */
    public function getFinalScore()
    {
        return $this->finalScore;
    }


}