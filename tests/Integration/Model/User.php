<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\Model;

use Doctrine\Common\Collections\ArrayCollection;
use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @OGM\Node(label="User")
 */
class User
{
    /**
     * @OGM\GraphId()
     */
    protected $id;

    /**
     * @OGM\Property(type="string")
     */
    protected $login;

    /**
     * @OGM\Relationship(targetEntity="Company", type="WORKS_AT" , direction="OUTGOING")
     */
    protected $currentCompany;

    /**
     * @OGM\Relationship(targetEntity="User", type="FOLLOWS", direction="OUTGOING", collection=true)
     * @var User[]
     */
    protected $friends;

    /**
     * @OGM\Relationship(targetEntity="User", type="IN_LOVE_WITH", direction="OUTGOING", collection=true, mappedBy="lovedBy")
     */
    protected $loves;

    /**
     * @OGM\Relationship(targetEntity="User", type="IN_LOVE_WITH", direction="INCOMING", collection=true, mappedBy="loves")
     */
    protected $lovedBy;

    /**
     * @OGM\Label(name="Active")
     * @var bool
     */
    protected $isActive;

    /**
     * @OGM\Property(type="int")
     */
    protected $age;

    public function __construct($login, $age = null)
    {
        $this->login = $login;
        $this->age = $age;
        $this->friends = new ArrayCollection();
        $this->loves = new ArrayCollection();
        $this->lovedBy = new ArrayCollection();
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
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @return mixed
     */
    public function getAge()
    {
        return $this->age;
    }

    public function setAge($age)
    {
        $this->age = (int) $age;
    }

    public function setCurrentCompany(Company $company)
    {
        $this->currentCompany = $company;
    }

    public function getCurrentCompany()
    {
        return $this->currentCompany;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\GraphAware\Neo4j\OGM\Tests\Integration\Model\User[]
     */
    public function getFriends()
    {
        return $this->friends;
    }

    public function setInactive()
    {
        $this->isActive = false;
    }

    public function setActive()
    {
        $this->isActive = true;
    }

    /**
     * @return mixed
     */
    public function getLoves()
    {
        return $this->loves;
    }

    /**
     * @return mixed
     */
    public function getLovedBy()
    {
        return $this->lovedBy;
    }

    public function addLoves(User $user)
    {
        if (!$this->loves->contains($user)) {
            $this->loves->add($user);
        }
    }

    public function addLovedBy(User $user)
    {
        if (!$this->lovedBy->contains($user)) {
            $this->lovedBy->add($user);
        }
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }
}