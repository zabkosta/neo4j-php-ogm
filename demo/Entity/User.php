<?php

namespace Demo\Entity;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @OGM\Property(type="int")
     */
    protected $age;

    /**
     * @OGM\RelatedNode(targetEntity="Demo\User", collection=true, direction="OUTGOINT")
     * @var \Doctrine\Common\Collections\ArrayCollection[\Demo\User]
     */
    protected $friends;

    /**
     * @OGM\RelatedNode(targetEntity="Demo\Company", type="WORKS_AT", direction="OUTGOING", mappedBy="members")
     */
    protected $company;

    public function __construct($login)
    {
        $this->login = $login;
        $this->friends = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param mixed $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return mixed
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param mixed $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getFriends()
    {
        return $this->friends;
    }

    /**
     * @param \Demo\Entity\User $friend
     */
    public function addFriend(User $friend)
    {
        $this->friends->add($friend);
    }

    /**
     * @param \Demo\Entity\User $friend
     */
    public function removeFriend(User $friend)
    {
        $this->friends->removeElement($friend);
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $friends
     */
    public function setFriends(ArrayCollection $friends)
    {
        $this->friends = $friends;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

}
