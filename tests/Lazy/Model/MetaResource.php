<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\Tests\Lazy\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @OGM\Node(label="MetaResource")
 */
class MetaResource
{
    /**
     * @OGM\GraphId()
     *
     * @var int
     */
    protected $id;

    /**
     * @OGM\Property(type="string")
     *
     * @var string
     */
    protected $resourceType;

    /**
     * @OGM\Property(type="string")
     *
     * @var string
     */
    protected $name_DE;

    /**
     * @OGM\Property(type="string")
     *
     * @var string
     */
    protected $icon;

    /**
     * @OGM\Property(type="string")
     *
     * @var string
     */
    protected $iconColour;

    /**
     * @OGM\Property(type="string")
     *
     * @var string
     */
    protected $colour;

    public function __construct($resourceType)
    {
        $this->resourceType = $resourceType;
    }

    public function getResourceType()
    {
        return $this->resourceType;
    }

    public function getName_DE()
    {
        return $this->name_DE;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function getIconColour()
    {
        return $this->iconColour;
    }

    public function getColour()
    {
        return $this->colour;
    }
}
