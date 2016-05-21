<?php

namespace GraphAware\Neo4j\OGM\Tests\Mapping;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @OGM\Node(label="Dummy", repository="DummyRepository")
 */
class NodeEntityWithCustomRepo
{
    /**
     * @OGM\GraphId()
     */
    protected $id;
}