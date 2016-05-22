<?php

namespace GraphAware\Neo4j\OGM;

use Doctrine\Common\Collections\ArrayCollection;
use GraphAware\Neo4j\Client\Stack;
use GraphAware\Neo4j\OGM\Annotations\Relationship;
use GraphAware\Neo4j\OGM\Persister\EntityPersister;
use GraphAware\Neo4j\OGM\Persister\RelationshipEntityPersister;
use GraphAware\Neo4j\OGM\Persister\RelationshipPersister;

class UnitOfWork
{
    const STATE_NEW = 'STATE_NEW';

    const STATE_MANAGED = 'STATE_MANAGED';

    const STATE_DELETED = 'STATE_DELETED';

    protected $manager;

    protected $managedEntities = [];

    protected $entityStates = [];

    protected $hashesMap = [];

    protected $entityIds = [];

    protected $nodesScheduledForCreate = [];

    protected $nodesScheduledForUpdate = [];

    protected $nodesScheduledForDelete = [];

    protected $relationshipsScheduledForCreated = [];

    protected $relEntitiesScheduledForCreate = [];

    protected $relEntitiesById = [];

    protected $relEntitiesMap = [];

    protected $persisters = [];

    protected $relationshipEntityPersisters = [];

    protected $relationshipPersister;

    protected $entitiesById = [];

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
        $this->relationshipPersister = new RelationshipPersister();
    }

    public function persist($entity)
    {
        $visited = array();

        $this->doPersist($entity, $visited);
    }

    public function doPersist($entity, array &$visited)
    {
        $oid = spl_object_hash($entity);
        $this->hashesMap[$oid] = $entity;

        if (isset($visited[$oid])) {
            return;
        }

        $visited[$oid] = $entity;

        $classMetadata = $this->manager->getClassMetadataFor(get_class($entity));
        $entityState = $this->getEntityState($entity, self::STATE_NEW);

        switch ($entityState) {
            case self::STATE_MANAGED:
                $this->nodesScheduledForUpdate[$oid] = $entity;
                break;
            case self::STATE_NEW:
                $this->nodesScheduledForCreate[$oid] = $entity;
                break;
            case self::STATE_DELETED:
                $this->nodesScheduledForDelete[$oid] = $entity;
        }

        $this->cascadePersist($entity, $visited);
        $this->traverseRelationshipEntities($entity);
    }

    public function cascadePersist($entity, array &$visited)
    {
        $classMetadata = $this->manager->getClassMetadataFor(get_class($entity));
        $associations = $classMetadata->getAssociatedObjects($entity);

        foreach ($associations as $association) {
            if (is_array($association[1]) || $association[1] instanceof ArrayCollection) {
                foreach ($association[1] as $assoc) {
                    $this->persistRelationship($entity, $association[0], $assoc, $visited);
                }
            } else {
                $this->persistRelationship($entity, $association[0], $association[1], $visited);
            }
        }
    }

    public function persistRelationship($entityA, Relationship $relationship, $entityB, array &$visited)
    {
        $oid = spl_object_hash($entityB);
        $this->doPersist($entityB, $visited);
        $this->relationshipsScheduledForCreated[] = [$entityA, $relationship, $entityB];
    }

    public function flush()
    {
        $statements = [];

        foreach ($this->nodesScheduledForCreate as $nodeToCreate) {
            $class = get_class($nodeToCreate);
            $persister = $this->getPersister($class);
            $statements[] = $persister->getCreateQuery($nodeToCreate);
        }

        $tx = $this->manager->getDatabaseDriver()->transaction();
        $tx->begin();

        $stack = $this->manager->getDatabaseDriver()->stack('create_schedule');
        foreach ($statements as $statement) {
            $stack->push($statement->text(), $statement->parameters(), $statement->getTag());
        }
        $results = $tx->runStack($stack);

        foreach ($results as $result) {
            $oid = $result->statement()->getTag();
            $gid = $result->records()[0]->value('id');
            $this->hydrateGraphId($oid, $gid);
            $this->entityIds[$oid] = $gid;
            $this->entityStates[$oid] = self::STATE_MANAGED;
        }

        $relStack = $this->manager->getDatabaseDriver()->stack('rel_create_schedule');
        foreach ($this->relationshipsScheduledForCreated as $relationship) {
            $statement = $this->relationshipPersister->getRelationshipQuery(
                $this->entityIds[spl_object_hash($relationship[0])],
                $relationship[1],
                $this->entityIds[spl_object_hash($relationship[2])]
            );
            $relStack->push($statement->text(), $statement->parameters());
        }
        $results = $tx->runStack($relStack);

        $reStack = Stack::create('rel_entity_create');
        foreach ($this->relEntitiesScheduledForCreate as $oid => $entity) {
            $rePersister = $this->getRelationshipEntityPersister(get_class($entity));
            $statement = $rePersister->getCreateQuery($entity);
            $reStack->push($statement->text(), $statement->parameters());
        }
        $tx->runStack($reStack);

        $updateNodeStack = Stack::create('update_nodes');
        foreach ($this->nodesScheduledForUpdate as $entity) {
            $statement = $this->getPersister(get_class($entity))->getUpdateQuery($entity);
            $updateNodeStack->push($statement->text(), $statement->parameters());
        }
        $tx->pushStack($updateNodeStack);

        $tx->commit();

        $this->nodesScheduledForCreate
            = $this->nodesScheduledForUpdate
            = $this->nodesScheduledForDelete
            = $this->relationshipsScheduledForCreated
            = array();
    }

    public function traverseRelationshipEntities($entity)
    {
        $classMetadata = $this->manager->getClassMetadataFor(get_class($entity));
        $reflClass = new \ReflectionClass($entity);
        foreach ($classMetadata->getRelationshipEntities() as $key => $relationship) {
            $property = $reflClass->getProperty($key);
            $property->setAccessible(true);
            $value = $property->getValue($entity);
            if (null === $value || ($relationship->getCollection() && count($value) === 0)) {
                return;
            }
            if ($relationship->getCollection()) {
                foreach ($value as $v) {
                    $this->persistRelationshipEntity($v);
                }
            }
        }
    }

    public function persistRelationshipEntity($entity)
    {
        $oid = spl_object_hash($entity);

        $this->relEntitiesScheduledForCreate[$oid] = $entity;
    }

    public function getEntityState($entity, $assumedState = null)
    {
        $oid = spl_object_hash($entity);

        if (isset($this->entityStates[$oid])) {
            return $this->entityStates[$oid];
        }

        if (null !== $assumedState) {
            return $assumedState;
        }

        $id = $this->manager->getClassMetadataFor(get_class($entity))->getIdentityValue($entity);

        if (!$id) {
            return self::STATE_NEW;
        }

        throw new \LogicException('entity state cannot be assumed');
    }

    public function addManaged($entity)
    {
        $oid = spl_object_hash($entity);
        $classMetadata = $this->manager->getClassMetadataFor(get_class($entity));
        $id = $classMetadata->getIdentityValue($entity);
        if (null === $id) {
            throw new \LogicException('Entity marked for managed but couldnt find identity');
        }
        $this->entityStates[$oid] = self::STATE_MANAGED;
        $this->entityIds[$oid] = $id;
        $this->entitiesById[$id] = $entity;
    }

    /**
     * @param int $id
     *
     * @return object|null
     */
    public function getEntityById($id)
    {
        return isset($this->entitiesById[$id]) ? $this->entitiesById[$id] : null;
    }

    /**
     * @param $class
     *
     * @return Persister\EntityPersister
     */
    public function getPersister($class)
    {
        if (!array_key_exists($class, $this->persisters)) {
            $classMetadata = $this->manager->getClassMetadataFor($class);
            $this->persisters[$class] = new EntityPersister($class, $classMetadata);
        }

        return $this->persisters[$class];
    }

    /**
     * @param $class
     *
     * @return \GraphAware\Neo4j\OGM\Persister\RelationshipEntityPersister
     */
    public function getRelationshipEntityPersister($class)
    {
        if (!array_key_exists($class, $this->relationshipEntityPersisters)) {
            $classMetadata = $this->manager->getRelationshipEntityMetadata($class);
            $this->relationshipEntityPersisters[$class] = new RelationshipEntityPersister($this->manager, $class, $classMetadata);
        }

        return $this->relationshipEntityPersisters[$class];
    }

    public function hydrateGraphId($oid, $gid)
    {
        $refl0 = new \ReflectionObject($this->nodesScheduledForCreate[$oid]);
        $p = $refl0->getProperty('id');
        $p->setAccessible(true);
        $p->setValue($this->nodesScheduledForCreate[$oid], $gid);
    }
}
