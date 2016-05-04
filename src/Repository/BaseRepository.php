<?php

namespace GraphAware\Neo4j\OGM\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use GraphAware\Common\Result\RecordViewInterface;
use GraphAware\Common\Type\Node;
use GraphAware\Common\Result\Result;
use GraphAware\Neo4j\OGM\Manager;
use GraphAware\Neo4j\OGM\Metadata\ClassMetadata;

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
     * @param \GraphAware\Neo4j\OGM\Metadata\ClassMetadata $classMetadata
     * @param \GraphAware\Neo4j\OGM\Manager $manager
     * @param string $className
     */
    public function __construct(ClassMetadata $classMetadata, Manager $manager, $className)
    {
        $this->classMetadata = $classMetadata;
        $this->manager = $manager;
        $this->className = $className;
    }

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
            $query .= 'OPTIONAL MATCH (n)' . $relQueryPart . '(' . $identifier . ')';
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
            $query .= 'OPTIONAL MATCH (n)' . $relQueryPart . '(' . $identifier . ')';
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

    public function findOneBy($key, $value)
    {
        $instances = $this->findBy($key, $value);

        if (count($instances) > 1) {
            throw new \Exception('Expected only one result, got ' . count($instances));
        }

        return isset($instances[0]) ? $instances[0] : null;
    }

    public function hydrateResultSet(Result $result)
    {
        $entities = [];
        foreach ($result->records() as $record) {
                $entities[] = $this->hydrate($record);
        }

        return $entities;
    }

    public function hydrate(RecordViewInterface $record)
    {
        $reflClass = new \ReflectionClass($this->className);
        $baseInstance = $this->hydrateNode($record->value('n'));
        foreach ($this->classMetadata->getAssociations() as $key => $association) {
            if (null !== $record->value($key)) {
                if ($association->getCollection()) {
                    foreach ($record->value($key) as $v) {
                        $property = $reflClass->getProperty($key);
                        $property->setAccessible(true);
                        $property->getValue($baseInstance)->add($this->hydrateNode($v));
                    }
                } else {
                    $property = $reflClass->getProperty($key);
                    $property->setAccessible(true);
                    $hydrator = $this->getHydrator($association->getTargetEntity());
                    $relO = $hydrator->hydrateNode($record->value($key));
                    $property->setValue($baseInstance, $relO);
                    $this->setInversedAssociation($baseInstance, $relO, $key);
                }
            }
        }

        return $baseInstance;
    }

    public function getHydrator($target)
    {
        return $this->manager->getRepository($target);
    }

    public function hydrateNode(Node $node)
    {
        if ($entity = $this->manager->getUnitOfWork()->getEntityById($node->identity())) {
            return $entity;
        }
        $reflClass = new \ReflectionClass($this->className);
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

        //$this->manager->getUnitOfWork()->addManaged($instance);

        return $instance;
    }

    public function setInversedAssociation($baseInstance, $otherInstance, $relationshipKey)
    {
        $assoc = $this->classMetadata->getAssociation($relationshipKey);
        if ($assoc->hasMappedBy()) {
            $mappedBy = $assoc->getMappedBy();
            $reflClass = new \ReflectionClass(get_class($otherInstance));
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
}