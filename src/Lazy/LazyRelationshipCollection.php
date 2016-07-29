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

    protected $baseEntityClass;

    protected $relationshipMetadata;

    protected $baseInstance;

    public function __construct(EntityManager $em, $baseEntity, $targetEntityClass, RelationshipMetadata $relationshipMetadata, $initialEntity = null)
    {
        $this->finder = $relationshipMetadata->isRelationshipEntity() ? new RelationshipEntityFinder($em, $targetEntityClass, $relationshipMetadata, $baseEntity) : new RelationshipsFinder($em, $targetEntityClass, $relationshipMetadata);
        $this->em = $em;
        $this->collection = new Collection();
        $this->baseId = $this->em->getClassMetadataFor(get_class($baseEntity))->getIdValue($baseEntity);
        $this->initialEntity = $initialEntity;
        $this->baseEntityClass = get_class($baseEntity);
        $this->relationshipMetadata = $relationshipMetadata;
        $this->baseInstance = $baseEntity;
    }

    protected function doInitialize()
    {
        $i = 0;
        $instances = $this->finder->find($this->baseId);
        foreach ($instances as $instance) {
            $cm = $this->em->getClassMetadata(get_class($instance));
            if (!$this->collection->contains($instance)) {
                if (!$this->relationshipMetadata->isRelationshipEntity()) {
                    $this->em->getUnitOfWork()->addManagedRelationshipReference($this->baseInstance, $instance, $this->relationshipMetadata->getPropertyName(), $this->relationshipMetadata);
                }
                $this->collection->add($instance);
                ++$i;
            }
        }
    }

    public function addInit($element)
    {
        $this->collection->add($element);
    }


}