<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Resource
 * @package GraphAware\Neo4j\OGM\Tests\Integration\Model
 *
 * @OGM\Node(label="Resource")
 */
class Resource
{
    /**
     * @var int
     *
     * @OGM\GraphId()
     */
    protected $id;

    /**
     * @var string
     *
     * @OGM\Property(type="string")
     */
    protected $name;

    /**
     * @var UserResource[]|ArrayCollection
     *
     * @OGM\Relationship(relationshipEntity="UserResource", direction="INCOMING", mappedBy="resource", collection=true)
     */
    protected $userResources;

    public function __construct($name)
    {
        $this->name = $name;
        $this->userResources = new ArrayCollection();
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
     * @return \Doctrine\Common\Collections\ArrayCollection|\GraphAware\Neo4j\OGM\Tests\Integration\Model\UserResource[]
     */
    public function getUserResources()
    {
        return $this->userResources;
    }
}