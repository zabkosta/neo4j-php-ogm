<?php

/**
 * This file is part of the GraphAware Neo4j OGM package.
 *
 * (c) GraphAware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\Lazy;

use Doctrine\Common\Collections\AbstractLazyCollection;
use GraphAware\Neo4j\OGM\Common\Collection;
use GraphAware\Neo4j\OGM\EntityManager;
use GraphAware\Neo4j\OGM\Finder\RelationshipsFinder;
use GraphAware\Neo4j\OGM\Metadata\RelationshipMetadata;

class LazyRelationshipCollection extends AbstractLazyCollection
{
    private $em;

    private $finder;

    private $baseId;

    public function __construct(EntityManager $em, $baseEntity, $targetEntityClass, RelationshipMetadata $relationshipMetadata)
    {
        $this->finder = new RelationshipsFinder($em, $targetEntityClass, $relationshipMetadata);
        $this->em = $em;
        $this->collection = new Collection();
        $this->baseId = $this->em->getClassMetadataFor(get_class($baseEntity))->getIdValue($baseEntity);
    }

    protected function doInitialize()
    {
        $instances = $this->finder->find($this->baseId);
        foreach ($instances as $instance) {
            $this->collection->add($instance);
        }
    }
}