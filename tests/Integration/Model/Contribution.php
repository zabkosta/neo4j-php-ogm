<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * Class Contribution
 * @package GraphAware\Neo4j\OGM\Tests\Integration\Model
 *
 * @OGM\RelationshipEntity(type="CONTRIBUTED_TO")
 */
class Contribution
{
    /**
     * @var int
     *
     * @OGM\GraphId()
     */
    protected $id;

    /**
     * @var User
     *
     * @OGM\StartNode(targetEntity="User")
     */
    protected $user;

    /**
     * @var Repository
     *
     * @OGM\EndNode(targetEntity="Repository")
     */
    protected $repository;

    /**
     * @var int
     *
     * @OGM\Property(type="int")
     */
    protected $score;

    public function __construct(User $user, Repository $repository, $score)
    {
        $this->user = $user;
        $this->repository = $repository;
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
     * @return \GraphAware\Neo4j\OGM\Tests\Integration\Model\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Tests\Integration\Model\Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }




}