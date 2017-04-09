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

    protected $em;

    protected $cql;

    protected $parameters = [];

    protected $mappings = [];

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
    }

    /**
     * @param string $key
     * @param string $value
     * @param null|int $type
     */
    public function setParameter($key, $value, $type = null)
    {
        $this->parameters[$key] = [$value, $type];
    }

    /**
     * @param string $alias
     * @param string $className
     */
    public function addEntityMapping($alias, $className, $hydrationType = self::HYDRATE_SINGLE)
    {
        $this->mappings[$alias] = [$className, $hydrationType];
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


        return $result[0];
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

        if (count($this->mappings) === 1) {
            $k = array_keys($this->mappings)[0];
            return $cqlResult[$k];
        }

        return $cqlResult;
    }

    private function handleResult(Result $result)
    {
        $queryResult = [];

        foreach ($result->records() as $record) {
            $keys = $record->keys();
            $this->validateKeys($keys);

            foreach ($keys as $key) {

                $mode = $this->mappings[$key][1];

                if ($mode === self::HYDRATE_SINGLE) {
                    $queryResult[$key] = $this->em->getEntityHydrator($this->mappings[$key][0])->hydrateNode($record->get($key));
                } elseif ($mode === self::HYDRATE_COLLECTION) {
                    foreach ($record->get($key) as $i) {
                        if (!$i instanceof Node) {
                            throw new \InvalidArgumentException(sprintf('Node class expected for "%s" result', $key));
                        }

                        $queryResult[$key][] = $this->em->getEntityHydrator($this->mappings[$key][0])->hydrateNode($i);
                    }
                }
            }
        }

        return $queryResult;
    }

    private function validateKeys(array $keys)
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $this->mappings)) {
                throw new \RuntimeException(sprintf('The query mapping do not contain a reference for "%s"', $key));
            }
        }
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