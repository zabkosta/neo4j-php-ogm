<?php

namespace GraphAware\Neo4j\OGM\Tests\Proxy;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @OGM\Node(label="User")
 */
class User
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
    protected $login;

    /**
     * @var Profile
     *
     * @OGM\Relationship(type="HAS_PROFILE", direction="OUTGOING", targetEntity="Profile", mappedBy="user")
     */
    protected $profile;

    /**
     * User constructor.
     * @param string $login
     */
    public function __construct($login)
    {
        $this->login = $login;
        $this->profile = new Profile($login.'@graphaware.com');
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
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @return Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }
}