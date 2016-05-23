<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @OGM\RelationshipEntity(type="ACTED_IN")
 */
class Role
{
    /**
     * @OGM\GraphId()
     */
    protected $id;

    /**
     * @OGM\StartNode(targetEntity="Person")
     */
    protected $actor;

    /**
     * @OGM\EndNode(targetEntity="Movie")
     */
    protected $movie;

    /**
     * @OGM\Property(type="array")
     */
    protected $roles;
}