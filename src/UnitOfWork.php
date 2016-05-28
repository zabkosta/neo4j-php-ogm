<?php

namespace GraphAware\Neo4j\OGM;

use Doctrine\Common\Collections\ArrayCollection;
use GraphAware\Neo4j\Client\Stack;
use GraphAware\Neo4j\OGM\Metadata\RelationshipMetadata;
use GraphAware\Neo4j\OGM\Persister\EntityPersister;
use GraphAware\Neo4j\OGM\Persister\RelationshipEntityPersister;
use GraphAware\Neo4j\OGM\Persister\RelationshipPersister;

class UnitOfWork
{
    const STATE_NEW = 'STATE_NEW';

    const STATE_MANAGED = 'STATE_MANAGED';

    const STATE_DELETED = 'STATE_DELETED';

    protected $entityManager;

    protected $managedEntities = [];

    protected $entityStates = [];

    protected $hashesMap = [];

    protected $entityIds = [];

    protected $nodesScheduledForCreate = [];

    protected $nodesScheduledForUpdate = [];

    protected $nodesScheduledForDelete = [];

    protected $relationshipsScheduledForCreated = [];

    protected $relationshipsScheduledForDelete = [];

    protected $relEntitiesScheduledForCreate = [];

    protected $relEntitesScheduledForUpdate = [];

    protected $relEntitesScheduledForDelete = [];

    protected $relEntitiesById = [];

    protected $relEntitiesMap = [];

    protected $persisters = [];

    protected $relationshipEntityPersisters = [];

    protected $relationshipPersister;

    protected $entitiesById = [];

    protected $managedRelationshipReferences = [];

    protected $entityStateReferences = [];

    protected $managedRelationshipEntities = [];

    protected $relationshipEntityReferences = [];

    protected $relationshipEntityStates = [];

    protected $reEntityIds = [];

    protected $reEntitiesById = [];

    protected $managedRelationshipEntitiesMap = [];

    public function __construct(EntityManager $manager)
    {
        $this->entityManager = $manager;
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
        $this->traverseRelationshipEntities($entity, $visited);
    }

    public function cascadePersist($entity, array &$visited)
    {
        $classMetadata = $this->entityManager->getClassMetadataFor(get_class($entity));
        $associations = $classMetadata->getSimpleRelationships();

        foreach ($associations as $association) {
            $value = $association->getValue($entity);
            if (is_array($value) || $value instanceof ArrayCollection) {
                foreach ($value as $assoc) {
                    $this->persistRelationship($entity, $assoc, $association, $visited);
                }
            } else {
                $entityB = $association->getValue($entity);
                if (is_object($entityB)) {
                    $this->persistRelationship($entity, $entityB, $association, $visited);
                }
            }
        }
    }

    public function persistRelationship($entityA, $entityB, RelationshipMetadata $relationship, array &$visited)
    {
        $this->doPersist($entityB, $visited);
        $this->relationshipsScheduledForCreated[] = [$entityA, $relationship, $entityB, $relationship->getPropertyName()];
    }

    public function flush()
    {
        $this->checkRelationshipReferencesHaveChanged();
        $this->detectRelationshipEntityChanges();
        $this->detectEntityChanges();
        $statements = [];

        foreach ($this->nodesScheduledForCreate as $nodeToCreate) {
            $class = get_class($nodeToCreate);
            $persister = $this->getPersister($class);
            $statements[] = $persister->getCreateQuery($nodeToCreate);
        }

        $tx = $this->entityManager->getDatabaseDriver()->transaction();
        $tx->begin();

        $stack = $this->entityManager->getDatabaseDriver()->stack('create_schedule');
        foreach ($statements as $statement) {
            $stack->push($statement->text(), $statement->parameters(), $statement->getTag());
        }
        $results = $tx->runStack($stack);

        foreach ($results as $result) {
            $oid = $result->statement()->getTag();
            $gid = $result->records()[0]->value('id');
            $this->hydrateGraphId($oid, $gid);
            $this->entitiesById[$gid] = $this->nodesScheduledForCreate[$oid];
            $this->entityIds[$oid] = $gid;
            $this->entityStates[$oid] = self::STATE_MANAGED;
            $this->manageEntityReference($oid);
        }

        $relStack = $this->entityManager->getDatabaseDriver()->stack('rel_create_schedule');
        foreach ($this->relationshipsScheduledForCreated as $relationship) {
            $statement = $this->relationshipPersister->getRelationshipQuery(
                $this->entityIds[spl_object_hash($relationship[0])],
                $relationship[1],
                $this->entityIds[spl_object_hash($relationship[2])]
            );
            $relStack->push($statement->text(), $statement->parameters());
        }

        if (count($this->relationshipsScheduledForDelete) > 0) {
            foreach ($this->relationshipsScheduledForDelete as $toDelete) {
                $statement = $this->relationshipPersister->getDeleteRelationshipQuery($toDelete[0], $toDelete[1], $toDelete[2]);
                $relStack->push($statement->text(), $statement->parameters());
            }
        }
        $tx->runStack($relStack);
        $reStack = Stack::create('rel_entity_create');
        foreach ($this->relEntitiesScheduledForCreate as $oid => $info) {
            $rePersister = $this->getRelationshipEntityPersister(get_class($info[0]));
            $statement = $rePersister->getCreateQuery($info[0], $info[1]);
            $reStack->push($statement->text(), $statement->parameters());
        }
        foreach ($this->relEntitesScheduledForUpdate as $oid => $entity) {
            $rePersister = $this->getRelationshipEntityPersister(get_class($entity));
            $statement = $rePersister->getUpdateQuery($entity);
            $reStack->push($statement->text(), $statement->parameters());
        }
        foreach ($this->relEntitesScheduledForDelete as $o) {
            $statement = $this->getRelationshipEntityPersister(get_class($o))->getDeleteQuery($o);
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

        foreach ($this->relationshipsScheduledForCreated as $rel) {
            $aoid = spl_object_hash($rel[0]);
            $boid = spl_object_hash($rel[2]);
            $field = $rel[3];
            $this->managedRelationshipReferences[$aoid][$field][] = [
                'entity' => $aoid,
                'target' => $boid,
                'rel' => $rel[1],
            ];
        }

        $this->nodesScheduledForCreate
            = $this->nodesScheduledForUpdate
            = $this->nodesScheduledForDelete
            = $this->relationshipsScheduledForCreated
            = $this->relationshipsScheduledForDelete
            = $this->relEntitesScheduledForUpdate
            = $this->relEntitiesScheduledForCreate
            = $this->relEntitesScheduledForDelete
            = array();
    }

    private function manageEntityReference($oid)
    {
        $id = $this->entityIds[$oid];
        $entity = $this->entitiesById[$id];
        $this->entityStateReferences[$id] = clone $entity;
    }

    private function detectEntityChanges()
    {
        $managed = [];
        foreach ($this->entityStates as $oid => $state) {
            if ($state === self::STATE_MANAGED) {
                $managed[] = $oid;
            }
        }

        foreach ($managed as $oid) {
            $id = $this->entityIds[$oid];
            $entityA = $this->entitiesById[$id];
            $entityB = $this->entityStateReferences[$id];
            $this->computeChanges($entityA, $entityB);
        }
    }

    private function computeChanges($entityA, $entityB)
    {
        $classMetadata = $this->entityManager->getClassMetadataFor(get_class($entityA));
        $propertyFields = array_merge($classMetadata->getPropertiesMetadata(), $classMetadata->getLabeledProperties());
        foreach ($propertyFields as $field => $meta) {
            $p1 = $meta->getValue($entityA);
            $p2 = $meta->getValue($entityB);
            if ($p1 !== $p2) {
                $this->nodesScheduledForUpdate[spl_object_hash($entityA)] = $entityA;
            }
        }
    }

    public function addManagedRelationshipReference($entityA, $entityB, $field, RelationshipMetadata $relationship)
    {
        $aoid = spl_object_hash($entityA);
        $boid = spl_object_hash($entityB);
        $this->managedRelationshipReferences[$aoid][$field][] = [
            'entity' => $aoid,
            'target' => $boid,
            'rel' => $relationship,
        ];
    }

    private function detectRelationshipEntityChanges()
    {
        $managed = [];
        foreach ($this->relationshipEntityStates as $oid => $state) {
            if ($state === self::STATE_MANAGED) {
                $managed[] = $oid;
            }
        }

        foreach ($managed as $oid) {
            $reA = $this->reEntitiesById[$this->reEntityIds[$oid]];
            $reB = $this->relationshipEntityReferences[$this->reEntityIds[$oid]];
            $this->computeRelationshipEntityChanges($reA, $reB);
            $this->checkRelationshipEntityDeletions($reA);
        }
    }

    private function computeRelationshipEntityChanges($entityA, $entityB)
    {
        $classMetadata = $this->entityManager->getRelationshipEntityMetadata(get_class($entityA));
        foreach ($classMetadata->getPropertiesMetadata() as $meta) {
            if ($meta->getValue($entityA) !== $meta->getValue($entityB)) {
                $this->relEntitesScheduledForUpdate[spl_object_hash($entityA)] = $entityA;
            }
        }
    }

    public function addManagedRelationshipEntity($entity, $pointOfView, $field)
    {
        $id = $this->entityManager->getRelationshipEntityMetadata(get_class($entity))->getIdValue($entity);
        $oid = spl_object_hash($entity);
        $this->relationshipEntityStates[$oid] = self::STATE_MANAGED;
        $ref = clone $entity;
        $this->reEntitiesById[$id] = $entity;
        $this->reEntityIds[$oid] = $id;
        $this->relationshipEntityReferences[$id] = $ref;
        $poid = spl_object_hash($pointOfView);
        $this->managedRelationshipEntities[$poid][$field][] = $oid;
        $this->managedRelationshipEntitiesMap[$oid][$poid] = $field;
    }

    private function checkRelationshipEntityDeletions($entity)
    {
        $oid = spl_object_hash($entity);
        $id = $this->entityManager->getRelationshipEntityMetadata(get_class($entity))->getIdValue($entity);
        foreach ($this->managedRelationshipEntitiesMap[$oid] as $pov => $field) {
            $e = $this->entitiesById[$this->entityIds[$pov]];
            $reflClass = new \ReflectionClass(get_class($e));
            $reflP = $reflClass->getProperty($field);
            $values = $reflP->getValue($e);
            $shouldBeDeleted = true;
            foreach ($values as $v) {
                $id2 = $this->entityManager->getRelationshipEntityMetadata(get_class($entity))->getIdValue($v);
                if ($id2 === $id) {
                    $shouldBeDeleted = false;
                }
            }
            if ($shouldBeDeleted) {
                $this->relEntitesScheduledForDelete[] = $entity;
            }
        }
    }

    private function checkRelationshipReferencesHaveChanged()
    {
        foreach ($this->managedRelationshipReferences as $oid => $reference) {
            $entity = $this->entitiesById[$this->entityIds[$oid]];
            $reflO = new \ReflectionObject($entity);
            foreach ($reference as $field => $info) {
                $property = $reflO->getProperty($field);
                $property->setAccessible(true);
                $value = $property->getValue($entity);
                if (is_array($value) || $value instanceof ArrayCollection) {
                    if (count($value) < count($info)) {
                        foreach ($info as $ref) {
                            $target = $this->entitiesById[$this->entityIds[$ref['target']]];
                            $toBeDeleted = null;
                            if (is_array($value)) {
                                if (!in_array($target, $value)) {
                                    $toBeDeleted = $target;
                                }
                            } elseif ($value instanceof ArrayCollection) {
                                if (!$value->contains($target)) {
                                    $toBeDeleted = $target;
                                }
                            }
                            if (null !== $toBeDeleted) {
                                $this->scheduleRelationshipReferenceForDelete($entity, $toBeDeleted, $ref['rel']);
                            }
                        }
                    }
                }
            }
        }
    }

    public function scheduleRelationshipReferenceForDelete($entity, $target, RelationshipMetadata $relationship)
    {
        $eClass = $this->entityManager->getClassMetadataFor(get_class($entity));
        $tClass = $this->entityManager->getClassMetadataFor(get_class($target));
        $this->relationshipsScheduledForDelete[] = [$eClass->getIdValue($entity), $tClass->getIdValue($target), $relationship];
    }

    public function traverseRelationshipEntities($entity, array &$visited)
    {
        $classMetadata = $this->entityManager->getClassMetadataFor(get_class($entity));
        foreach ($classMetadata->getRelationshipEntities() as $relationshipMetadata) {
            $value = $relationshipMetadata->getValue($entity);
            if (null === $value || ($relationshipMetadata->isCollection() && count($value) === 0)) {
                return;
            }
            if ($relationshipMetadata->isCollection()) {
                foreach ($value as $v) {
                    $this->persistRelationshipEntity($v, get_class($entity));
                    $rem = $this->entityManager->getRelationshipEntityMetadata(get_class($v));
                    $toPersistProperty = $rem->getStartNode() === $classMetadata->getClassName() ? $rem->getEndNodeValue($v) : $rem->getStartNodeValue($v);
                    $this->persist($toPersistProperty, $visited);
                }
            }
        }
    }

    public function persistRelationshipEntity($entity, $pov)
    {
        $oid = spl_object_hash($entity);

        $this->relEntitiesScheduledForCreate[$oid] = [$entity, $pov];
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

        $id = $this->entityManager->getClassMetadataFor(get_class($entity))->getIdentityValue($entity);

        if (!$id) {
            return self::STATE_NEW;
        }

        throw new \LogicException('entity state cannot be assumed');
    }

    public function addManaged($entity)
    {
        $oid = spl_object_hash($entity);
        $classMetadata = $this->entityManager->getClassMetadataFor(get_class($entity));
        $id = $classMetadata->getIdValue($entity);
        if (null === $id) {
            throw new \LogicException('Entity marked for managed but couldnt find identity');
        }
        $this->entityStates[$oid] = self::STATE_MANAGED;
        $this->entityIds[$oid] = $id;
        $this->entitiesById[$id] = $entity;
        $this->manageEntityReference($oid);
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
            $classMetadata = $this->entityManager->getClassMetadataFor($class);
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
            $classMetadata = $this->entityManager->getRelationshipEntityMetadata($class);
            $this->relationshipEntityPersisters[$class] = new RelationshipEntityPersister($this->entityManager, $class, $classMetadata);
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
