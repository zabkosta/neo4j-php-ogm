<?php

namespace GraphAware\Neo4j\OGM\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use GraphAware\Common\Cypher\Statement;
use GraphAware\Common\Result\Record;
use GraphAware\Common\Type\Node;
use GraphAware\Common\Result\Result;
use GraphAware\Neo4j\OGM\Manager;
use GraphAware\Neo4j\OGM\Metadata\ClassMetadata;
use GraphAware\Neo4j\OGM\Query\ResultMapping;

class BaseRepository
{
    /**
     * @var \GraphAware\Neo4j\OGM\Metadata\ClassMetadata
     */
    protected $classMetadata;

    /**
     * @var \GraphAware\Neo4j\OGM\Manager
     */
    protected $manager;

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

    /**
     * @param \GraphAware\Neo4j\OGM\Metadata\ClassMetadata $classMetadata
     * @param \GraphAware\Neo4j\OGM\Manager                $manager
     * @param string                                       $className
     */
    public function __construct(ClassMetadata $classMetadata, Manager $manager, $className)
    {
        $this->classMetadata = $classMetadata;
        $this->manager = $manager;
        $this->className = $className;
    }

    /**
     * @return object[]
     *
     * @throws \GraphAware\Neo4j\Client\Exception\Neo4jException
     */
    public function findAll()
    {
        $label = $this->classMetadata->getLabel();
        $query = sprintf('MATCH (n:%s)', $label);

        foreach ($this->classMetadata->getAssociations() as $identifier => $association) {
            switch ($association->getDirection()) {
                case 'INCOMING':
                    $relStr = '<-[:%s]-';
                    break;
                case 'OUTGOING':
                    $relStr = '-[:%s]->';
                    break;
                default:
                    $relStr = '-[:%s]-';
                    break;
            }

            $relQueryPart = sprintf($relStr, $association->getType());
            $query .= PHP_EOL;
            $query .= 'OPTIONAL MATCH (n)'.$relQueryPart.'('.$identifier.')';
        }

        $query .= PHP_EOL;
        $query .= 'RETURN n';
        $assocReturns = [];
        foreach ($this->classMetadata->getAssociations() as $k => $association) {
            if ($association->getCollection()) {
                $assocReturns[] = sprintf('collect(%s) as %s', $k, $k);
            } else {
                $assocReturns[] = $k;
            }
        }

        if (count($this->classMetadata->getAssociations()) > 0) {
            $query .= ', ';
            $query .= implode(', ', $assocReturns);
        }

        $result = $this->manager->getDatabaseDriver()->run($query);

        return $this->hydrateResultSet($result);
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return object[]
     *
     * @throws \GraphAware\Neo4j\Client\Exception\Neo4jException
     */
    public function findBy($key, $value)
    {
        $label = $this->classMetadata->getLabel();
        $query = sprintf('MATCH (n:%s) WHERE n.%s = {%s}', $label, $key, $key);

        foreach ($this->classMetadata->getAssociations() as $identifier => $association) {
            switch ($association->getDirection()) {
                case 'INCOMING':
                    $relStr = '<-[:%s]-';
                    break;
                case 'OUTGOING':
                    $relStr = '-[:%s]->';
                    break;
                default:
                    $relStr = '-[:%s]-';
                    break;
            }

            $relQueryPart = sprintf($relStr, $association->getType());
            $query .= PHP_EOL;
            $query .= 'OPTIONAL MATCH (n)'.$relQueryPart.'('.$identifier.')';
        }

        $query .= PHP_EOL;
        $query .= 'RETURN n';
        $assocReturns = [];
        foreach ($this->classMetadata->getAssociations() as $k => $association) {
            if ($association->getCollection()) {
                $assocReturns[] = sprintf('collect(%s) as %s', $k, $k);
            } else {
                $assocReturns[] = $k;
            }
        }

        if (count($this->classMetadata->getAssociations()) > 0) {
            $query .= ', ';
            $query .= implode(', ', $assocReturns);
        }

        $parameters = [$key => $value];

        $result = $this->manager->getDatabaseDriver()->run($query, $parameters);

        return $this->hydrateResultSet($result);
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return null|object
     *
     * @throws \Exception
     */
    public function findOneBy($key, $value)
    {
        $instances = $this->findBy($key, $value);

        if (count($instances) > 1) {
            throw new \Exception('Expected only one result, got '.count($instances));
        }

        return isset($instances[0]) ? $instances[0] : null;
    }

    protected function nativeQuery(Statement $statement, ResultMapping $resultMapping)
    {
        $result = $this->manager->getDatabaseDriver()->run($statement->text(), $statement->parameters(), $statement->getTag());
        if (0 === $result->size()) {
            return null;
        }
        if ($result->size() > 1) {
            throw new \RuntimeException(sprintf('nativeQuery only allows single records'));
        }
        $record = $result->firstRecord();
        if (!$record->hasValue($resultMapping->getRootIdentifier())) {
            throw new \RuntimeException(sprintf('The record doesn\'t contain the value for the root identifier "%s", please check your query "%s"', $resultMapping->getRootIdentifier(), $statement->text()));
        }

        $rootInstance = $this->hydrate($record->get($resultMapping->getRootIdentifier()), false);

        return $rootInstance;

    }

    protected function hydrateResultSet(Result $result)
    {
        $entities = [];
        foreach ($result->records() as $record) {
            $entities[] = $this->hydrate($record);
        }

        return $entities;
    }

    protected function hydrate(Record $record, $andCheckAssociations = true)
    {
        $reflClass = new \ReflectionClass($this->className);
        $baseInstance = $this->hydrateNode($record->get('n'));
        if ($andCheckAssociations) {
            foreach ($this->classMetadata->getAssociations() as $key => $association) {
                if (null !== $record->get($key)) {
                    if ($association->getCollection()) {
                        foreach ($record->get($key) as $v) {
                            $property = $reflClass->getProperty($key);
                            $property->setAccessible(true);
                            $property->getValue($baseInstance)->add($this->hydrateNode($v));
                        }
                    } else {
                        $property = $reflClass->getProperty($key);
                        $property->setAccessible(true);
                        $hydrator = $this->getHydrator($this->getTargetFullClassName($association->getTargetEntity()));
                        $relO = $hydrator->hydrateNode($record->get($key));
                        $property->setValue($baseInstance, $relO);
                        $this->setInversedAssociation($baseInstance, $relO, $key);
                    }
                }
            }
        }

        return $baseInstance;
    }

    protected function getHydrator($target)
    {
        return $this->manager->getRepository($target);
    }

    protected function hydrateNode(Node $node)
    {
        if ($entity = $this->manager->getUnitOfWork()->getEntityById($node->identity())) {
            return $entity;
        }
        $reflClass = $this->getReflectionClass($this->className);
        $instance = $reflClass->newInstanceWithoutConstructor();
        foreach ($this->classMetadata->getFields() as $field => $meta) {
            if ($node->hasValue($field)) {
                if ($property = $reflClass->getProperty($field)) {
                    $property->setAccessible(true);
                    $property->setValue($instance, $node->value($field));
                }
            }
        }

        foreach ($this->classMetadata->getAssociations() as $key => $assoc) {
            if ($assoc->getCollection()) {
                $property = $reflClass->getProperty($key);
                $property->setAccessible(true);
                $property->setValue($instance, new ArrayCollection());
            }
        }

        foreach ($this->classMetadata->getRelationshipEntities() as $key => $assoc) {
            if ($assoc->getCollection()) {
                $property = $reflClass->getProperty($key);
                $property->setAccessible(true);
                $property->setValue($instance, new ArrayCollection());
            }
        }

        $property = $reflClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($instance, $node->identity());

        $this->manager->getUnitOfWork()->addManaged($instance);

        return $instance;
    }

    protected function setInversedAssociation($baseInstance, $otherInstance, $relationshipKey)
    {
        $assoc = $this->classMetadata->getAssociation($relationshipKey);
        if ($assoc->hasMappedBy()) {
            $mappedBy = $assoc->getMappedBy();
            $reflClass = $this->getReflectionClass(get_class($otherInstance));
            $property = $reflClass->getProperty($mappedBy);
            $property->setAccessible(true);
            $otherClassMetadata = $this->manager->getClassMetadataFor(get_class($otherInstance));
            if ($otherClassMetadata->getAssociation($mappedBy)->getCollection()) {
                if (null === $property->getValue($otherInstance)) {
                    $property->setValue($otherInstance, new ArrayCollection());
                }
                $property->getValue($otherInstance)->add($baseInstance);
            } else {
                $property->setValue($otherInstance, $baseInstance);
            }
        }
    }

    /**
     * @param $className
     *
     * @return \ReflectionClass
     */
    protected function getReflectionClass($className)
    {
        if (!array_key_exists($className, $this->loadedReflClasses)) {
            $this->loadedReflClasses[$className] = new \ReflectionClass($className);
        }

        return $this->loadedReflClasses[$className];
    }

    /**
     * @param $className
     *
     * @return string
     */
    protected function getTargetFullClassName($className)
    {
        $expl = explode('\\', $className);
        if (1 === count($expl)) {
            $expl2 = explode('\\', $this->className);
            if (1 !== count($expl2)) {
                unset($expl2[count($expl2) - 1]);
                $className = implode('\\', $expl2).'\\'.$className;
            }
        }

        if (!class_exists($className)) {
            throw new \LogicException(sprintf('Guessed class name "%s" doesn\'t exist', $className));
        }

        return $className;
    }
}
