<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class SecurityRole
 * @package GraphAware\Neo4j\OGM\Tests\Integration\Model
 *
 * @OGM\Node(label="SecurityRole")
 */
class SecurityRole
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
     * @var User[]|ArrayCollection
     *
     * @OGM\Relationship(targetEntity="User", direction="INCOMING", type="HAS_ROLE", collection=true)
     */
    protected $users;

    public function __construct($name)
    {
        $this->name = $name;
        $this->users = new ArrayCollection();
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
     * @return \GraphAware\Neo4j\OGM\Tests\Integration\Model\User[]
     */
    public function getUsers()
    {
        return $this->users;
    }
}