<?php

namespace GraphAware\Neo4j\OGM\Hydrator;

use GraphAware\Common\Result\Record;
use GraphAware\Common\Result\Result;
use GraphAware\Common\Type\Node;
use GraphAware\Neo4j\OGM\EntityManager;
use GraphAware\Neo4j\OGM\Metadata\NodeEntityMetadata;

class EntityHydrator
{
    /**
     * @var EntityManager
     */
    protected $_em;

    /**
     * @var NodeEntityMetadata
     */
    protected $_classMetadata;

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

    protected function hydrateRecord(Record $record, array &$result)
    {
        $cqlAliasMap = $this->getAliases();

        foreach ($record->keys() as $cqlAlias) {
            $data = $record->get($cqlAlias);
            $entityName = $cqlAliasMap[$cqlAlias];
            $id = $data->identity();

            // Check the entity is not managed yet by the uow
            if (null !== $entity = $this->_em->getUnitOfWork()->getEntityById($id)) {
                $result[] = $entity;
                continue;
            }

            // create the entity
            $entity = $this->_em->getUnitOfWork()->createEntity($data, $entityName, $id);
            $this->hydrateProperties($entity, $data);

            $result[] = $entity;
        }
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