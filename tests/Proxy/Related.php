<?php

namespace GraphAware\Neo4j\OGM\Tests\Proxy;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 *
 * @OGM\Node(label="Related")
 *
 * Class Related
 * @package GraphAware\Neo4j\OGM\Tests\Proxy
 *
 */
class Related
{
    /**
     * @var int
     *
     * @OGM\GraphId()
     */
    protected $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}