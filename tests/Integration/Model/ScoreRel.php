<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\tests\Integration\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * Class ScoreRel.
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

    public function setFinalScore($score)
    {
        $this->finalScore = $score;
    }
}
