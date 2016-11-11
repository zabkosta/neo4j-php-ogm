<?php

namespace GraphAware\Neo4j\OGM\Tests\Proxy;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @OGM\Node(label="Init")
 *
 * Class Init
 * @package GraphAware\Neo4j\OGM\Tests\Proxy
 */
class Init
{
    /**
     * @var
     *
     * @OGM\GraphId()
     */
    protected $id;

    /**
     * @OGM\Relationship(type="RELATES", direction="OUTGOING", targetEntity="Related")
     * @OGM\Lazy()
     *
     * @var Related
     */
    protected $relation;

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
    public function getRelation()
    {
        return $this->relation;
    }

}