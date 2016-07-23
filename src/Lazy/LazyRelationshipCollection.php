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
use GraphAware\Neo4j\OGM\Finder\RelationshipEntityFinder;
use GraphAware\Neo4j\OGM\Finder\RelationshipsFinder;
use GraphAware\Neo4j\OGM\Metadata\RelationshipMetadata;

class LazyRelationshipCollection extends AbstractLazyCollection
{
    protected $em;

    protected $finder;

    protected $baseId;

    protected $initialEntity;

    public function __construct(EntityManager $em, $baseEntity, $targetEntityClass, RelationshipMetadata $relationshipMetadata, $initialEntity = null)
    {
        $this->finder = $relationshipMetadata->isRelationshipEntity() ? new RelationshipEntityFinder($em, $targetEntityClass, $relationshipMetadata, $baseEntity) : new RelationshipsFinder($em, $targetEntityClass, $relationshipMetadata);
        $this->em = $em;
        $this->collection = new Collection();
        $this->baseId = $this->em->getClassMetadataFor(get_class($baseEntity))->getIdValue($baseEntity);
        $this->initialEntity = $initialEntity;
    }

    protected function doInitialize()
    {
        $instances = $this->finder->find($this->baseId);
        foreach ($instances as $instance) {
            if (!$this->collection->contains($instance)) {
                $this->collection->add($instance);
            }
        }
    }
}