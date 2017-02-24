<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\Repository;

use GraphAware\Common\Result\Record;
use GraphAware\Common\Result\Result;
use GraphAware\Common\Type\Node;
use GraphAware\Neo4j\OGM\Annotations\Label;
use GraphAware\Neo4j\OGM\EntityManager;
use GraphAware\Neo4j\OGM\Finder\RelationshipsFinder;
use GraphAware\Neo4j\OGM\Lazy\LazyRelationshipCollection;
use GraphAware\Neo4j\OGM\Metadata\EntityPropertyMetadata;
use GraphAware\Neo4j\OGM\Metadata\NodeEntityMetadata;
use GraphAware\Neo4j\OGM\Metadata\QueryResultMapper;
use GraphAware\Neo4j\OGM\Metadata\RelationshipEntityMetadata;
use GraphAware\Neo4j\OGM\Metadata\RelationshipMetadata;
use GraphAware\Neo4j\OGM\Query\Pagination;
use GraphAware\Neo4j\OGM\Query\QueryResultMapping;
use GraphAware\Neo4j\OGM\Util\ClassUtils;
use GraphAware\Neo4j\OGM\Util\ProxyUtils;
use ProxyManager\Configuration;
use ProxyManager\Factory\LazyLoadingGhostFactory;
use ProxyManager\FileLocator\FileLocator;
use ProxyManager\GeneratorStrategy\FileWriterGeneratorStrategy;
use ProxyManager\Version;

class BaseRepository
{
    const FILTER_LIMIT = 'limit';

    const FILTER_ORDER = 'order';

    const ORDER_ASC = 'ASC';

    const ORDER_DESC = 'DESC';

    private static $PAGINATION_FIRST_RESULT_KEY = 'first';
    private static $PAGINATION_LIMIT_RESULTS_KEY = 'max';

    /**
     * @var \GraphAware\Neo4j\OGM\Metadata\ClassMetadata
     */
    protected $classMetadata;

    /**
     * @var \GraphAware\Neo4j\OGM\EntityManager
     */
    protected $entityManager;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var \ReflectionClass
     */
    protected $reflectionClass;

    /**
     * @var \ReflectionClass[]
     */
    protected $loadedReflClasses = [];

    protected $lazyLoadingFactory;

    /**
     * @param \GraphAware\Neo4j\OGM\Metadata\ClassMetadata $classMetadata
     * @param \GraphAware\Neo4j\OGM\EntityManager          $manager
     * @param string                                       $className
     */
    public function __construct(NodeEntityMetadata $classMetadata, EntityManager $manager, $className)
    {
        $this->classMetadata = $classMetadata;
        $this->entityManager = $manager;
        $this->className = $className;
        $config = new Configuration();
        $dir = sys_get_temp_dir();
        $config->setGeneratorStrategy(new FileWriterGeneratorStrategy(new FileLocator($dir)));
        $config->setProxiesTargetDir($dir);
        spl_autoload_register($config->getProxyAutoloader());

        $this->lazyLoadingFactory = new LazyLoadingGhostFactory($config);
    }

    public function findAll()
    {
        return $this->findBy([]);
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $persister = $this->entityManager->getEntityPersister($this->className);

        return $persister->loadAll($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param $className
     *
     * @return \ReflectionClass
     */
    private function getReflectionClass($className)
    {
        if (!array_key_exists($className, $this->loadedReflClasses)) {
            $this->loadedReflClasses[$className] = new \ReflectionClass($className);
        }

        return $this->loadedReflClasses[$className];
    }


}
