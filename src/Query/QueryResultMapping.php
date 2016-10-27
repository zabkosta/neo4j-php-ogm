<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\Query;

class QueryResultMapping
{
    const RESULT_SINGLE = 'RESULT_SINGLE';

    const RESULT_MULTIPLE = 'RESULT_MULTIPLE';

    /**
     * @var string
     */
    protected $queryResultClass;

    /**
     * @var string
     */
    protected $queryResultType;

    /**
     * @param string $queryResultClass
     * @param string $queryResultType
     */
    public function __construct($queryResultClass, $queryResultType)
    {
        if (!class_exists($queryResultClass)) {
            throw new \RuntimeException(sprintf('The class "%s" could not be found', $queryResultClass));
        }
        $this->queryResultClass = $queryResultClass;
        if ($queryResultType !== self::RESULT_SINGLE && $queryResultType !== self::RESULT_MULTIPLE) {
            throw new \RuntimeException(sprintf('Query Result Type should be of type "%s" or "%s", "%s" given', self::RESULT_SINGLE, self::RESULT_MULTIPLE, (string) $queryResultType));
        }
        $this->queryResultType = $queryResultType;
    }

    /**
     * @return string
     */
    public function getQueryResultClass()
    {
        return $this->queryResultClass;
    }

    /**
     * @return string
     */
    public function getQueryResultType()
    {
        return $this->queryResultType;
    }
}
