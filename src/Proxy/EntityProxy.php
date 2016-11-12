<?php

namespace GraphAware\Neo4j\OGM\Proxy;

interface EntityProxy
{
    public function __initializeProperty($propertyName);
}