<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\Model;

use GraphAware\Neo4j\OGM\Tests\Integration\Model\Movie;
use Doctrine\Common\Collections\ArrayCollection;
use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @OGM\Node(label="Person")
 */
class Person
{
    /**
     * @OGM\GraphId()
     */
    public $id;

    /**
     * @OGM\Property(type="string")
     */
    public $name;

    /**
     * @OGM\Property(type="int")
     */
    public $born;

    /**
     * @OGM\Relationship(relationshipEntity="Role", direction="OUTGOING", type="ACTED_IN", collection=true)
     * @var \Doctrine\Common\Collections\ArrayCollection|Role[]
     */
    public $roles;

    /**
     * @OGM\Relationship(targetEntity="Movie", direction="OUTGOING", type="PLAYED_IN", collection=true, mappedBy="players")
     */
    public $movies;

    public function __construct($name = null)
    {
        if (null !== $name) {
            $this->name = $name;
        }
        $this->roles = new ArrayCollection();
        $this->movies = new ArrayCollection();
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
     * @return mixed
     */
    public function getBorn()
    {
        return $this->born;
    }

    /**
     * @return Role
     */
    public function getRoles()
    {
        return $this->roles;
    }

    public function addRole(Movie $movie, $roles = null)
    {
        $roles = is_array($roles) ? $roles : [];
        $this->roles->add(new Role($this, $movie, $roles));
    }
}