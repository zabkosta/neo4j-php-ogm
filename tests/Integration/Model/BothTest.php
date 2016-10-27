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

use Doctrine\Common\Collections\ArrayCollection;
use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * Class BothTest.
 *
 * @OGM\Node(label="Both")
 */
class BothTest
{
    /**
     * @OGM\GraphId()
     */
    protected $id;

    /**
     * @OGM\Property(type="string")
     */
    protected $name;

    /**
     * @OGM\Relationship(targetEntity="BothTest", direction="BOTH", collection=true, type="RELATES")
     */
    protected $others;

    /**
     * @OGM\Relationship(relationshipEntity="BothRel", direction="BOTH", collection=true, type="FRIEND")
     */
    protected $friends;

    public function __construct($name)
    {
        $this->name = $name;
        $this->others = new ArrayCollection();
        $this->friends = new ArrayCollection();
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
    public function getName()
    {
        return $this->name;
    }

    public function hasOtherWithName($name)
    {
        foreach ($this->others as $other) {
            if ($name === $other->getName()) {
                return true;
            }
        }

        return false;
    }

    public function addOther(BothTest $other)
    {
        $this->others->add($other);
    }

    /**
     * @return mixed
     */
    public function getOthers()
    {
        return $this->others;
    }

    public function addFriend(BothTest $bothTest)
    {
        $this->friends->add(new BothRel($this, $bothTest));
    }

    public function getFriends()
    {
        return $this->friends;
    }
}
