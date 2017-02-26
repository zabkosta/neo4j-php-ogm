<?php

namespace GraphAware\Neo4j\OGM\Metadata\Factory;

use GraphAware\Neo4j\OGM\Metadata\NodeEntityMetadata;
use GraphAware\Neo4j\OGM\Metadata\QueryResultMapper;
use GraphAware\Neo4j\OGM\Metadata\RelationshipEntityMetadata;

interface GraphEntityMetadataFactoryInterface
{
    /**
     * @param string $className
     *
     * @return NodeEntityMetadata|RelationshipEntityMetadata
     */
    public function create($className);

    /**
     * @param string $className
     *
     * @return bool
     */
    public function supports($className);

    /**
     * @param string $className
     *
     * @return QueryResultMapper
     */
    public function createQueryResultMapper($className);

    /**
     * @param string $className
     *
     * @return bool
     */
    public function supportsQueryResult($className);
}
