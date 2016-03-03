<?php

namespace Demo\Entity;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @OGM\Node(label="Company")
 */
class Company
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
     * @OGM\Relationship(targetEntity="Demo\User", type="WORKS_AT", direction="INCOMING", collection=true)
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $members;

    public function __construct($name)
    {
        $this->name = $name;
        $this->members = new ArrayCollection();
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

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * @param \Demo\Entity\User $user
     */
    public function addMember(User $user)
    {
        $this->members->add($user);
    }

    /**
     * @param \Demo\Entity\User $user
     */
    public function removeMember(User $user)
    {
        $this->members->removeElement($user);
    }
}