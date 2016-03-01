<?php

namespace GraphAware\Neo4j\OGM;

use GraphAware\Neo4j\OGM\Persister\EntityPersister;

class UnitOfWork
{
    const STATE_NEW = "STATE_NEW";

    const STATE_MANAGED = "STATE_MANAGED";

    const STATE_DELETED = "STATE_DELETED";

    protected $manager;

    protected $managedEntities = [];

    protected $entityStates = [];

    protected $hashesMap = [];

    protected $nodesScheduledForCreate = [];

    protected $nodesScheduledForUpdate = [];

    protected $nodesScheduledForDelete = [];

    protected $persisters = [];

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function persist($entity)
    {
        $oid = spl_object_hash($entity);
        $entityState = $this->getEntityState($entity, self::STATE_NEW);

        switch ($entityState) {
            case self::STATE_NEW:
                $this->nodesScheduledForCreate[$oid] = $entity;
                break;
            case self::STATE_MANAGED:
                $this->nodesScheduledForUpdate[$oid] = $entity;
                break;
        }
    }

    public function flush()
    {
        $statements = [];

        foreach ($this->nodesScheduledForCreate as $nodeToCreate) {
            $class = get_class($nodeToCreate);
            $persister = $this->getPersister($class);
            $statements[] = $persister->getCreateQuery($nodeToCreate);
        }

        $stack = $this->manager->getDatabaseDriver()->stack('create_schedule');
        foreach ($statements as $statement) {
            $stack->push($statement->text(), $statement->parameters(), $statement->getTag());
        }
        $results = $this->manager->getDatabaseDriver()->runStack($stack);

        foreach ($results as $result) {
            $oid = $result->statement()->getTag();
            var_dump($oid);
            $gid = $result->records()[0]->value('id');
            $this->hydrateGraphId($oid, $gid);
            unset($this->nodesScheduledForCreate[$oid]);
        }
    }

    public function getEntityState($entity, $defaultState = null)
    {
        $oid = spl_object_hash($entity);
        if (!array_key_exists($oid, $this->entityStates)) {
            $this->entityStates[$oid] = $defaultState !== null ? $defaultState : self::STATE_NEW;
        }

        return $this->entityStates[$oid];
    }

    /**
     * @param $class
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

    public function hydrateGraphId($oid, $gid)
    {
        $refl0 = new \ReflectionObject($this->nodesScheduledForCreate[$oid]);
        $p = $refl0->getProperty('id');
        $p->setAccessible(true);
        $p->setValue($this->nodesScheduledForCreate[$oid], $gid);
    }
}