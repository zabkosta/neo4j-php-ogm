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
 * Class Resource.
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
