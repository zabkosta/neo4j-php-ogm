<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace GraphAware\Neo4j\OGM\Persister;

use GraphAware\Neo4j\Client\Stack;
use GraphAware\Neo4j\OGM\EntityManager;

class FlushOperationProcessor
{
    const TAG_NODES_CREATE = "ogm_uow_nodes_create";

    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function processNodesCreationJob(array $nodesScheduledForCreate)
    {
        $byLabelsMap = [];
        foreach ($nodesScheduledForCreate as $node) {
            $metadata = $this->em->getClassMetadataFor(get_class($node));
            $byLabelsMap[$metadata->getLabel()][] = $node;
        }

        return $this->createLabeledNodesCreationStack($byLabelsMap);
    }

    private function createLabeledNodesCreationStack(array $byLabelsMap)
    {
        $stack = Stack::create(self::TAG_NODES_CREATE);
        foreach ($byLabelsMap as $label => $entities) {
            $query = sprintf('UNWIND {nodes} as node
            CREATE (n:`%s`) SET n += node.props
            RETURN id(n) as id, node.oid as oid', $label);

            $batch = [];
            foreach ($entities as $entity) {
                $metadata = $this->em->getClassMetadataFor(get_class($entity));
                $oid = spl_object_hash($entity);
                $batch[] = [
                    'props' => $metadata->getPropertyValuesArray($entity),
                    'oid' => $oid
                ];
            }
            $stack->push($query, ['nodes' => $batch]);
        }

        return $stack;
    }
}