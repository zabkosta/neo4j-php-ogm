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
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }
}