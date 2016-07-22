<?php

namespace GraphAware\Neo4j\OGM\Tests\Lazy\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use GraphAware\Neo4j\OGM\Common\Collection;

/**
 * Class User
 * @package GraphAware\Neo4j\OGM\Tests\Lazy\Model
 *
 * @OGM\Node(label="User")
 */
class User
{
    /**
     * @OGM\GraphId()
     * @var int
     */
    private $id;

    /**
     * @OGM\Relationship(type="HAS_RESOURCE", direction="OUTGOING", targetEntity="Resources", collection=true)
     */
    protected $resources;

    public function __construct()
    {
        $this->resources = new Collection();
    }

    /**
     * @return Resources[]
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * @return Resources[]
     */
    public function getResource($name)
    {
        /** @var MetaResource $resource */
        foreach($this->resources as $resource){
            if($resource->getResourceType() == $name){
                return $resource;
                break;
            }
        }
    }

    /**
     * @param Resources $resources
     */
    public function addResource(Resources $resources)
    {
        if (!$this->resources->contains($resources)) {
            $this->resources->add($resources);
        }
    }
}