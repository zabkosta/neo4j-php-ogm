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
}