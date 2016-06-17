<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\NativeQuery\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * Class User
 * @package GraphAware\Neo4j\OGM\Tests\Integration\NativeQuery\Model
 *
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
    protected $username;

    /**
     * @OGM\Property(type="string")
     */
    protected $email;

    /**
     * User constructor.
     * @param string $username
     * @param string $email
     */
    public function __construct($username, $email)
    {
        $this->username = $username;
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }
}