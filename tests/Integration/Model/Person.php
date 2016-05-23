<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\Model;

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
     * @var Role[]
     */
    public $roles;

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
     * @return mixed
     */
    public function getRoles()
    {
        return $this->roles;
    }
}