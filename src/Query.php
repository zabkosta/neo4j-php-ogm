<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM;

use GraphAware\Common\Result\Result;
use GraphAware\Common\Type\Node;
use GraphAware\Neo4j\OGM\Exception\Result\NonUniqueResultException;
use GraphAware\Neo4j\OGM\Exception\Result\NoResultException;

class Query
{
    const PARAMETER_LIST = 0;

    const PARAMETER_MAP = 1;

    const HYDRATE_COLLECTION = "HYDRATE_COLLECTION";

    const HYDRATE_SINGLE = "HYDRATE_SINGLE";

    const HYDRATE_RAW = "HYDRATE_RAW";

    protected $em;

    protected $cql;

    protected $parameters = [];

    protected $mappings = [];

    protected $resultMappings = [];

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param $cql
     */
    public function setCQL($cql)
    {
        $this->cql = $cql;

        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     * @param null|int $type
     */
    public function setParameter($key, $value, $type = null)
    {
        $this->parameters[$key] = [$value, $type];

        return $this;
    }

    /**
     * @param string $alias
     * @param string $className
     */
    public function addEntityMapping($alias, $className, $hydrationType = self::HYDRATE_SINGLE)
    {
        $this->mappings[$alias] = [$className, $hydrationType];

        return $this;
    }

    /**
     * @return array|mixed
     */
    public function getResult()
    {
        return $this->execute();
    }

    /**
     * @return mixed
     */
    public function getOneOrNullResult()
    {
        $result = $this->execute();

        if (empty($result)) {
            return null;
        }

        if (count($result) > 1) {
            throw new NonUniqueResultException(sprintf('Expected 1 or null result, got %d', count($result)));
        }


        return $result;
    }

    /**
     * @return mixed
     */
    public function getOneResult()
    {
        $result = $this->execute();

        if (count($result) > 1) {
            throw new NonUniqueResultException(sprintf('Expected 1 or null result, got %d', count($result)));
        }

        if (empty($result)) {
            throw new NoResultException();
        }

        return $result[0];
    }

    /**
     * @return array|mixed
     * @throws \Exception
     */
    public function execute()
    {
        $stmt = $this->cql;
        $parameters = $this->formatParameters();

        $result = $this->em->getDatabaseDriver()->run($stmt, $parameters);
        if ($result->size() === 0) {
            return [];
        }

        $cqlResult = $this->handleResult($result);

        return $cqlResult;
    }

    private function handleResult(Result $result)
    {
        $queryResult = [];

        foreach ($result->records() as $record) {
            $row = [];
            $keys = $record->keys();

            foreach ($keys as $key) {

                $mode = array_key_exists($key, $this->mappings) ? $this->mappings[$key][1] : self::HYDRATE_RAW;

                if ($mode === self::HYDRATE_SINGLE) {
                    if (count($keys) === 1) {
                        $row = $this->em->getEntityHydrator($this->mappings[$key][0])->hydrateNode($record->get($key));
                    } else {
                        $row[$key] = $this->em->getEntityHydrator($this->mappings[$key][0])->hydrateNode($record->get($key));
                    }
                } elseif ($mode === self::HYDRATE_COLLECTION) {
                    $coll = [];
                    foreach ($record->get($key) as $i) {
                        $v = $this->em->getEntityHydrator($this->mappings[$key][0])->hydrateNode($i);
                        $coll[] = $v;
                    }
                    $row[$key] = $coll;
                } elseif ($mode === self::HYDRATE_RAW) {
                    $row[$key] = $record->get($key);
                }
            }

            $queryResult[] = $row;
        }

        return $queryResult;
    }

    /**
     * @return array
     */
    private function formatParameters()
    {
        $params = [];
        foreach ($this->parameters as $alias => $parameter) {
            $params[$alias] = $parameter[0];
        }

        return $params;
    }
}