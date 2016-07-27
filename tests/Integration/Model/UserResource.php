<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Resource as ResourceModel;

/**
 * Class UserResource
 * @package GraphAware\Neo4j\OGM\Tests\Integration\Model
 *
 * @OGM\RelationshipEntity(type="HAS_RESOURCE")
 */
class UserResource
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
     * @var ResourceModel
     *
     * @OGM\EndNode(targetEntity="Resource")
     */
    protected $resource;

    /**
     * @var int
     *
     * @OGM\Property(type="int")
     */
    protected $amount;

    public function __construct(User $user, ResourceModel $resource, $amount)
    {
        $this->user = $user;
        $this->resource = $resource;
        $this->amount = $amount;
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
     * @return ResourceModel
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }


}