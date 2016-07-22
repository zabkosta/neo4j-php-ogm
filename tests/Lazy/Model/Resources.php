<?php

namespace GraphAware\Neo4j\OGM\Tests\Lazy\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @OGM\Node(label="Resources")
 */

class Resources
{
    /**
     * @OGM\GraphId()
     * @var int
     */
    protected $id;

    /**
     * @OGM\Property(type="int")
     * @var int
     */

    protected $resourcecount;


    /**
     * @OGM\Relationship(type="METARESOURCE", direction="OUTGOING", targetEntity="MetaResource", collection=false)
     * @var MetaResource
     */

    protected $metaResource;

    /**
     * @param MetaResource $metaResource
     */

    public function __construct(MetaResource $metaResource)
    {
        $this->metaResource = $metaResource;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getResourceCount()
    {
        return $this->resourcecount;
    }

    public function setResourceCount($resourcecount)
    {
        $this->resourcecount = $resourcecount;
    }

    /**
     * @return MetaResource
     */

    public function getMetaResource()
    {
        return $this->metaResource;
    }

}