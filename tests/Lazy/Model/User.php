<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\tests\Lazy\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use GraphAware\Neo4j\OGM\Common\Collection;

/**
 * Class User.
 *
 * @OGM\Node(label="User")
 */
class User
{
    /**
     * @OGM\GraphId()
     *
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
        foreach ($this->resources as $resource) {
            if ($resource->getResourceType() === $name) {
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
