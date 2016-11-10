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

/**
 * Class BothRel.
 *
 * @OGM\RelationshipEntity(type="FRIEND")
 */
class BothRel
{
    /**
     * @var
     *
     * @OGM\GraphId()
     */
    protected $id;

    /**
     * @var
     *
     * @OGM\StartNode(targetEntity="BothTest")
     */
    protected $startNode;

    /**
     * @var
     *
     * @OGM\EndNode(targetEntity="BothTest")
     */
    protected $endNode;

    public function __construct(BothTest $start, BothTest $end)
    {
        $this->startNode = $start;
        $this->endNode = $end;
    }

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
    public function getStartNode()
    {
        return $this->startNode;
    }

    /**
     * @return mixed
     */
    public function getEndNode()
    {
        return $this->endNode;
    }
}
