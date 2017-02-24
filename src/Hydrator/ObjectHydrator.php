<?php

namespace GraphAware\Neo4j\OGM\Hydrator;

use GraphAware\Common\Result\Result;
use GraphAware\Neo4j\OGM\EntityManager;

class ObjectHydrator
{
    protected $em;

    protected $className;

    protected $metadata;

    public function __construct($className, EntityManager $em)
    {
        $this->em = $em;
        $this->className = $className;
        $this->metadata = $this->em->getClassMetadataFor($className);
    }

    public function hydrateResultSet(Result $result)
    {
        $identifier = 'n';
    }
}