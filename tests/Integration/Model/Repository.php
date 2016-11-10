<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\Tests\Integration\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use GraphAware\Neo4j\OGM\Common\Collection;

/**
 * Class Repository.
 *
 * @OGM\Node(label="Repository")
 */
class Repository
{
    /**
     * @var int
     *
     * @OGM\GraphId()
     */
    protected $id;

    /**
     * @var string
     *
     * @OGM\Property(type="string")
     */
    protected $name;

    /**
     * @var Contribution[]|Collection
     *
     * @OGM\Relationship(relationshipEntity="Contribution", direction="INCOMING", collection=true, mappedBy="repository")
     * @OGM\OrderBy(property="score", order="DESC")
     */
    protected $contributions;

    public function __construct($name)
    {
        $this->name = $name;
        $this->contributions = new Collection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Common\Collection|\GraphAware\Neo4j\OGM\Tests\Integration\Model\Contribution[]
     */
    public function getContributions()
    {
        return $this->contributions;
    }
}
