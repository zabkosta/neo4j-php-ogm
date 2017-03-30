<?php

namespace GraphAware\Neo4j\OGM\Proxy;

use Doctrine\Common\Collections\AbstractLazyCollection;
use GraphAware\Common\Type\Node;
use GraphAware\Neo4j\OGM\Common\Collection;

class LazyCollection extends AbstractLazyCollection
{
    protected $initalizer;

    protected $node;

    protected $object;

    protected $initialized = false;

    protected $initializing = false;

    public function __construct(SingleNodeInitializer $initializer, Node $node, $object)
    {
        $this->initalizer = $initializer;
        $this->node = $node;
        $this->object = $object;
        $this->collection = new Collection();
    }

    protected function doInitialize()
    {
        if ($this->initialized || $this->initializing) {
            return;
        }
        $this->initializing = true;
        $this->initalizer->initialize($this->node, $this->object);
        $this->initialized = true;
        $this->initializing = false;
    }
}