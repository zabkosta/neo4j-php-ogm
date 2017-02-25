<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\Hydrator;

use GraphAware\Common\Result\Record;
use GraphAware\Common\Result\Result;
use GraphAware\Common\Type\Node;
use GraphAware\Neo4j\OGM\Common\Collection;
use GraphAware\Neo4j\OGM\EntityManager;
use GraphAware\Neo4j\OGM\Metadata\NodeEntityMetadata;

class EntityHydrator
{
    /**
     * @var EntityManager
     */
    private $_em;

    /**
     * @var NodeEntityMetadata
     */
    private $_classMetadata;

    public function __construct($className, EntityManager $em)
    {
        $this->_em = $em;
        $this->_classMetadata = $this->_em->getClassMetadataFor($className);
    }

    /**
     * @param Result $dbResult
     * @return array
     */
    public function hydrateAll(Result $dbResult)
    {
        $result = [];

        foreach ($dbResult->records() as $record) {
            $this->hydrateRecord($record, $result);
        }

        return $result;
    }

    /**
     * @param Result $dbResult
     * @param object $sourceEntity
     */
    public function hydrateSimpleRelationship($alias, Result $dbResult, $sourceEntity)
    {
        if (0 === $dbResult->size()) {
            return;
        }

        $relationshipMetadata = $this->_classMetadata->getRelationship($alias);
        $targetHydrator = $this->_em->getEntityHydrator($relationshipMetadata->getTargetEntity());
        $targetMeta = $this->_em->getClassMetadataFor($relationshipMetadata->getTargetEntity());
        $hydrated = $targetHydrator->hydrateAll($dbResult);

        $o = $hydrated[0];
        $relationshipMetadata->setValue($sourceEntity, $o);

        $mappedBy = $relationshipMetadata->getMappedByProperty();
        if ($mappedBy) {
            $targetMeta->getRelationship($mappedBy)->setValue($o, $sourceEntity);
        }
    }

    public function hydrateSimpleRelationshipCollection($alias, Result $dbResult, $sourceEntity)
    {
        $relationshipMetadata = $this->_classMetadata->getRelationship($alias);
        $this->initRelationshipCollection($alias, $sourceEntity);
        /** @var Collection $coll */
        $coll = $relationshipMetadata->getValue($sourceEntity);
        $targetHydrator = $this->_em->getEntityHydrator($relationshipMetadata->getTargetEntity());
        $targetMeta = $this->_em->getClassMetadataFor($relationshipMetadata->getTargetEntity());
        $nodes = $dbResult->firstRecord()->get($targetMeta->getEntityAlias());
        foreach ($nodes as $node) {
            $item = $targetHydrator->hydrateNode($node, $relationshipMetadata->getTargetEntity());
            $coll->add($item);
            $mappedBy = $relationshipMetadata->getMappedByProperty();
            if ($mappedBy) {
                $mappedRel = $targetMeta->getRelationship($mappedBy);
                if ($mappedRel->isCollection()) {
                    $mappedRel->initializeCollection($item);
                    $mappedRel->getValue($item)->add($sourceEntity);
                } else {
                    $mappedRel->setValue($item, $sourceEntity);
                }
            }
        }
    }

    private function initRelationshipCollection($alias, $sourceEntity)
    {
        $this->_classMetadata->getRelationship($alias)->initializeCollection($sourceEntity);
    }

    protected function hydrateRecord(Record $record, array &$result, $collection = false)
    {
        $cqlAliasMap = $this->getAliases();

        foreach ($record->keys() as $cqlAlias) {
            $data = $record->get($cqlAlias);
            $entityName = $cqlAliasMap[$cqlAlias];
            $data = $collection ? $data : [$data];
            foreach ($data as $node) {
                $id = $node->identity();

                // Check the entity is not managed yet by the uow
                if (null !== $entity = $this->_em->getUnitOfWork()->getEntityById($id)) {
                    $result[] = $entity;
                    continue;
                }

                // create the entity
                $entity = $this->_em->getUnitOfWork()->createEntity($node, $entityName, $id);
                $this->hydrateProperties($entity, $node);

                $result[] = $entity;
            }
        }
    }

    protected function hydrateNode(Node $node, $class)
    {
        $id = $node->identity();

        // Check the entity is not managed yet by the uow
        if (null !== $entity = $this->_em->getUnitOfWork()->getEntityById($id)) {
            return $entity;
        }

        // create the entity
        $entity = $this->_em->getUnitOfWork()->createEntity($node, $class, $id);
        $this->hydrateProperties($entity, $node);

        return $entity;
    }

    protected function hydrateProperties($object, Node $node)
    {
        foreach ($node->keys() as $key) {
            if ($this->_classMetadata->hasField($key)) {
                $propertyMeta = $this->_classMetadata->getPropertyMetadata($key);
                $propertyMeta->setValue($object, $node->get($key));
            }
        }
    }

    protected function getAliases()
    {
        return [$this->_classMetadata->getEntityAlias() => $this->_classMetadata->getClassName()];
    }

}