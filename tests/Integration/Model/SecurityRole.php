<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\Tests\Integration\Model;

use Doctrine\Common\Collections\ArrayCollection;
use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * Class SecurityRole.
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
