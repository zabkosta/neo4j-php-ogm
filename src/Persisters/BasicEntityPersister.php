<?php

namespace GraphAware\Neo4j\OGM\Persisters;

use GraphAware\Common\Cypher\Statement;
use GraphAware\Neo4j\OGM\EntityManager;
use GraphAware\Neo4j\OGM\Metadata\NodeEntityMetadata;

class BasicEntityPersister
{
    protected $_className;

    protected $_classMetadata;

    protected $_em;

    public function __construct($className, NodeEntityMetadata $classMetadata, EntityManager $em)
    {
        $this->_className = $className;
        $this->_classMetadata = $classMetadata;
        $this->_em = $em;
    }

    public function loadAll(array $criteria = [], array $orderBy = null, $limit = null, $offset = null)
    {
        $stmt = $this->getMatchCypher($criteria, $orderBy, $limit, $offset);
        $result = $this->_em->getDatabaseDriver()->run($stmt->text(), $stmt->parameters());

        $hydrator = $this->_em->getEntityHydrator($this->_className);

        return $hydrator->hydrateAll($result);
    }

    /**
     * @param $criteria
     * @param null|int $limit
     * @param null|int $offset
     * @param null|array $orderBy
     * @return Statement
     */
    public function getMatchCypher(array $criteria = [], $limit = null, $offset = null, $orderBy = null)
    {
        $identifier = $this->_classMetadata->getEntityAlias();
        $classLabel = $this->_classMetadata->getLabel();
        $cypher = 'MATCH ('.$identifier.':'.$classLabel.') RETURN '.$identifier;

        return Statement::create($cypher);
    }
}