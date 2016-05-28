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

use GraphAware\Neo4j\Client\ClientBuilder;
use GraphAware\Neo4j\OGM\Mapping\AnnotationDriver;
use GraphAware\Neo4j\Client\Client;
use GraphAware\Neo4j\OGM\Metadata\ClassMetadata;
use GraphAware\Neo4j\OGM\Metadata\Factory\GraphEntityMetadataFactory;
use GraphAware\Neo4j\OGM\Metadata\QueryResultMapper;
use GraphAware\Neo4j\OGM\Metadata\RelationshipEntityMetadata;
use GraphAware\Neo4j\OGM\Repository\BaseRepository;
use GraphAware\Neo4j\OGM\Util\ClassUtils;

class Manager
{
    /**
     * @var \GraphAware\Neo4j\OGM\UnitOfWork
     */
    protected $uow;

    /**
     * @var \GraphAware\Neo4j\Client\Client
     */
    protected $databaseDriver;

    /**
     * @var \GraphAware\Neo4j\OGM\Repository\BaseRepository[]
     */
    protected $repositories = [];

    /**
     * @var QueryResultMapper[]
     */
    protected $resultMappers = [];

    protected $loadedMetadata = [];

    /**
     * @var \GraphAware\Neo4j\OGM\Metadata\Factory\GraphEntityMetadataFactory
     */
    protected $metadataFactory;

    public static function create($host, $cacheDir = null)
    {
        $cache = $cacheDir ? : sys_get_temp_dir();
        $client = ClientBuilder::create()
            ->addConnection('default', $host)
            ->build();

        return new self($client, $cache);
    }

    /**
     * @param string $host
     *
     * @return \GraphAware\Neo4j\OGM\Manager
     */
    public static function buildWithHost($host)
    {
        $client = ClientBuilder::create()
            ->addConnection('default', $host)
            ->build();

        return new self($client);
    }

    public function __construct(Client $databaseDriver, $cacheDirectory = null)
    {
        $this->annotationDriver = new AnnotationDriver($cacheDirectory);
        $this->uow = new UnitOfWork($this);
        $this->databaseDriver = $databaseDriver;
        $this->metadataFactory = new GraphEntityMetadataFactory($this->annotationDriver->getReader());
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Mapping\AnnotationDriver
     */
    public function getAnnotationDriver()
    {
        return $this->annotationDriver;
    }

    public function save($entity)
    {
        if (!is_object($entity)) {
            throw new \Exception('Entity is not an object');
        }
    }

    public function persist($entity)
    {
        if (!is_object($entity)) {
            throw new \Exception('Manager::persist() expects an object');
        }

        $this->uow->persist($entity);
    }

    public function flush()
    {
        $this->uow->flush();
    }

    /**
     * @return \GraphAware\Neo4j\OGM\UnitOfWork
     */
    public function getUnitOfWork()
    {
        return $this->uow;
    }

    /**
     * @return \GraphAware\Neo4j\Client\Client
     */
    public function getDatabaseDriver()
    {
        return $this->databaseDriver;
    }

    public function getResultMappingMetadata($class)
    {
        if (!array_key_exists($class, $this->resultMappers)) {
            $this->resultMappers[$class] = $this->annotationDriver->readQueryResult($class);
            foreach ($this->resultMappers[$class]->getFields() as $field) {
                if ($field->isEntity()) {
                    $targetFQDN = ClassUtils::getFullClassName($field->getTarget(), $class);
                    $field->setMetadata($this->getClassMetadataFor($targetFQDN));
                }
            }
        }

        return $this->resultMappers[$class];
    }

    /**
     * @param $class
     *
     * @return \GraphAware\Neo4j\OGM\Metadata\NodeEntityMetadata
     */
    public function getClassMetadataFor($class)
    {
        if (!array_key_exists($class, $this->loadedMetadata)) {
            $this->loadedMetadata[$class] = $this->metadataFactory->create($class);
        }

        return $this->loadedMetadata[$class];
    }

    /**
     * @param string $class
     *
     * @return \GraphAware\Neo4j\OGM\Metadata\RelationshipEntityMetadata
     *
     * @throws \Exception
     */
    public function getRelationshipEntityMetadata($class)
    {
        if (!array_key_exists($class, $this->loadedMetadata)) {
            $this->loadedMetadata[$class] = $this->metadataFactory->create($class);
        }

        return $this->loadedMetadata[$class];
    }

    /**
     * @param string $class
     *
     * @return \GraphAware\Neo4j\OGM\Repository\BaseRepository
     */
    public function getRepository($class)
    {
        $classMetadata = $this->getClassMetadataFor($class);
        if (!array_key_exists($class, $this->repositories)) {
            $repositoryClassName = $classMetadata->hasCustomRepository() ? $classMetadata->getRepositoryClass() : BaseRepository::class;
            $this->repositories[$class] = new $repositoryClassName($classMetadata, $this, $class);
        }

        return $this->repositories[$class];
    }

    /**
     * Clear the Entity Manager
     * All entities that were managed by the unitOfWork become detached.
     */
    public function clear()
    {
        $this->uow = null;
        $this->uow = new UnitOfWork($this);
    }
}
