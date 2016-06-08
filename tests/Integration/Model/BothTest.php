<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\Model;

use Doctrine\Common\Collections\ArrayCollection;
use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * Class BothTest
 * @package GraphAware\Neo4j\OGM\Tests\Integration\Model
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

    public function __construct($name)
    {
        $this->name = $name;
        $this->others = new ArrayCollection();
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
}