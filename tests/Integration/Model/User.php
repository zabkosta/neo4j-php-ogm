<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\Model;

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
     * @OGM\Property(type="int")
     */
    protected $age;

    public function __construct($login, $age = null)
    {
        $this->login = $login;
        $this->age = $age;
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
}