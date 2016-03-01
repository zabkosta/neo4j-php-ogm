<?php

namespace GraphAware\Neo4j\OGM;

class UnitOfWork
{
    const STATE_NEW = "STATE_NEW";

    const STATE_MANAGED = "STATE_MANAGED";

    const STATE_DELETED = "STATE_DELETED";

    protected $manager;

    protected $managedEntities = [];

    protected $entityStates = [];

    protected $hashesMap = [];

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function persist($entity)
    {
        $oid = spl_object_hash($entity);

        if (array_key_exists($oid, $this->managedEntities)) {
            return;
        }

        $this->managedEntities[$oid] = $entity;
    }
}