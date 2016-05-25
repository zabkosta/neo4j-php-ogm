<?php

namespace GraphAware\Neo4j\OGM\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use GraphAware\Common\Result\Record;
use GraphAware\Common\Type\Node;
use GraphAware\Common\Result\Result;
use GraphAware\Neo4j\OGM\Annotations\Relationship;
use GraphAware\Neo4j\OGM\Manager;
use GraphAware\Neo4j\OGM\Metadata\ClassMetadata;
use GraphAware\Neo4j\OGM\Metadata\QueryResultMapper;
use GraphAware\Neo4j\OGM\Query\QueryResultMapping;
use GraphAware\Neo4j\OGM\Annotations\Property;
use GraphAware\Neo4j\OGM\Annotations\Label;
use GraphAware\Neo4j\OGM\Util\ClassUtils;

class BaseRepository
{
    const FILTER_LIMIT = 'limit';

    const FILTER_ORDER = 'order';

    const ORDER_ASC = 'ASC';

    const ORDER_DESC = 'DESC';

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
    public function findAll(array $filters = array())
    {
        $parameters = [];
        $label = $this->classMetadata->getLabel();
        $query = sprintf('MATCH (n:%s)', $label);
        /** @var Relationship[] $associations */
        $associations = array_merge($this->classMetadata->getAssociations(), $this->classMetadata->getRelationshipEntities());
        foreach ($associations as $identifier => $association) {
            switch ($association->getDirection()) {
                case 'INCOMING':
                    $relStr = '<-[rel_%s:%s]-';
                    break;
                case 'OUTGOING':
                    $relStr = '-[rel_%s:%s]->';
                    break;
                default:
                    $relStr = '-[rel_%s:%s]-';
                    break;
            }

            $relQueryPart = sprintf($relStr, strtolower($association->getType()), $association->getType());
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

        foreach ($this->classMetadata->getRelationshipEntities() as $relationshipEntity) {
            $relid = 'rel_'.strtolower($relationshipEntity->getType());
            if ($relationshipEntity->getCollection()) {
                $assocReturns[] = sprintf('CASE count(%s) WHEN 0 THEN [] ELSE collect({start:startNode(%s), end:endNode(%s), rel:%s}) END as %s', $relid, $relid, $relid, $relid, $relid);
            }
        }

        if (count($associations) > 0) {
            $query .= ', ';
            $query .= implode(', ', $assocReturns);
        }

        if (isset($filters[self::FILTER_ORDER])) {
            foreach ($filters[self::FILTER_ORDER] as $key => $filter) {
                if (array_key_exists($key, $this->classMetadata->getFields())) {
                    $query .= sprintf(' ORDER BY n.%s %s', $key, $filter);
                }
            }
        }

        if (isset($filters[self::FILTER_LIMIT]) && is_numeric($filters[self::FILTER_LIMIT])) {
            $query .= ' LIMIT {limit}';
            $parameters[self::FILTER_LIMIT] = $filters[self::FILTER_LIMIT];
        }

        $result = $this->manager->getDatabaseDriver()->run($query, $parameters);

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
    public function findBy($key, $value, $isId = false)
    {
        $label = $this->classMetadata->getLabel();
        $idId = $isId ? 'id(n)' : sprintf('n.%s', $key);
        $query = sprintf('MATCH (n:%s) WHERE %s = {%s}', $label, $idId, $key);
        /** @var Relationship[] $associations */
        $associations = array_merge($this->classMetadata->getAssociations(), $this->classMetadata->getRelationshipEntities());
        foreach ($associations as $identifier => $association) {
            switch ($association->getDirection()) {
                case 'INCOMING':
                    $relStr = '<-[rel_%s:%s]-';
                    break;
                case 'OUTGOING':
                    $relStr = '-[rel_%s:%s]->';
                    break;
                default:
                    $relStr = '-[rel_%s:%s]-';
                    break;
            }

            $relQueryPart = sprintf($relStr, strtolower($association->getType()), $association->getType());
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

        foreach ($this->classMetadata->getRelationshipEntities() as $relationshipEntity) {
            $relid = 'rel_'.strtolower($relationshipEntity->getType());
            if ($relationshipEntity->getCollection()) {
                $assocReturns[] = sprintf('CASE count(%s) WHEN 0 THEN [] ELSE collect({start:startNode(%s), end:endNode(%s), rel:%s}) END as %s', $relid, $relid, $relid, $relid, $relid);
            }
        }

        if (count($associations) > 0) {
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

    public function findOneById($id)
    {
        $hydrated = $this->findBy('id', $id, true);

        return isset($hydrated[0]) ? $hydrated[0] : null;
    }

    protected function nativeQuery($query, $parameters = null, QueryResultMapping $resultMapping)
    {
        $parameters = null !== $parameters ? (array) $parameters : array();
        $result = $this->manager->getDatabaseDriver()->run($query, $parameters);
        if ($result->size() < 1) {
            return;
        }

        if ($result->size() > 1 && $resultMapping->getQueryResultType() !== QueryResultMapping::RESULT_MULTIPLE) {
            throw new \RuntimeException(sprintf('Expected a single record, got %d', $result->size()));
        }

        $results = [];
        $mappingMetadata = $this->manager->getResultMappingMetadata($resultMapping->getQueryResultClass());
        foreach ($result->records() as $record) {
            $results[] = $this->hydrateQueryRecord($mappingMetadata, $record);
        }

        return $resultMapping->getQueryResultType() === QueryResultMapping::RESULT_SINGLE ? $results[0] : $results;
    }

    private function hydrateQueryRecord(QueryResultMapper $resultMapper, Record $record)
    {
        $reflClass = new \ReflectionClass($resultMapper->getClassName());
        $instance = $reflClass->newInstanceWithoutConstructor();
        foreach ($resultMapper->getFields() as $field) {
            if (!$record->hasValue($field->getFieldName())) {
                throw new \RuntimeException(sprintf('The record doesn\'t contain the required field "%s"', $field->getFieldName()));
            }
            $value = null;
            if ($field->isEntity()) {
                $value = $this->hydrate($record, false, $field->getFieldName(), ClassUtils::getFullClassName($field->getTarget(), $resultMapper->getClassName()));
            } else {
                $value = $record->get($field->getFieldName());
            }
            $property = $reflClass->getProperty($field->getFieldName());
            $property->setAccessible(true);
            $property->setValue($instance, $value);
        }

        return $instance;
    }

    private function hydrateResultSet(Result $result)
    {
        $entities = [];
        foreach ($result->records() as $record) {
            $entities[] = $this->hydrate($record);
        }

        return $entities;
    }

    private function hydrate(Record $record, $andCheckAssociations = true, $identifier = 'n', $className = null)
    {
        $classN = null !== $className ? $className : $this->className;
        $reflClass = new \ReflectionClass($classN);
        $baseInstance = $this->hydrateNode($record->get($identifier), $classN);
        if ($andCheckAssociations) {
            foreach ($this->classMetadata->getAssociations() as $key => $association) {
                if (!$association->isRelationshipEntity()) {
                    if ($record->hasValue($key) && null !== $record->get($key)) {
                        if ($association->getCollection()) {
                            foreach ($record->get($key) as $v) {
                                $property = $reflClass->getProperty($key);
                                $property->setAccessible(true);
                                $v2 = $this->hydrateNode($v, $this->getTargetFullClassName($association->getTargetEntity()));
                                $property->getValue($baseInstance)->add($v2);
                                $this->manager->getUnitOfWork()->addManagedRelationshipReference($baseInstance, $v2, $property->getName(), $association);
                                $this->setInversedAssociation($baseInstance, $v2, $key);
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

            foreach ($this->classMetadata->getRelationshipEntities() as $key => $relationshipEntity) {
                $recordKey = 'rel_'.strtolower($relationshipEntity->getType());
                if (null === $record->get($recordKey) || empty($record->get($recordKey))) {
                    continue;
                }
                $class = $this->getTargetFullClassName($relationshipEntity->getRelationshipEntity());
                $reMetadata = $this->manager->getRelationshipEntityMetadata($class);
                $startNodeClass = $this->getTargetFullClassName($reMetadata->getStartNode()->getTargetEntity());
                $endNodeClass = $this->getTargetFullClassName($reMetadata->getEndNode()->getTargetEntity());
                $reReflClass = new \ReflectionClass($class);
                if ($relationshipEntity->getCollection()) {
                    $v = new ArrayCollection();
                    if (!is_array($record->get($recordKey))) {
                        throw new \LogicException('Expected array record value');
                    }
                    foreach ($record->get($recordKey) as $reMap) {
                        $reInstance = $reReflClass->newInstanceWithoutConstructor();
                        $start = $this->hydrateNode($reMap['start'], $startNodeClass);
                        $end = $this->hydrateNode($reMap['end'], $endNodeClass);
                        /** @var \GraphAware\Neo4j\Client\Formatter\Type\Relationship $rel */
                        $rel = $reMap['rel'];
                        $relId = $rel->identity();
                        $startProperty = $reReflClass->getProperty($reMetadata->getStartNodeKey());
                        $startProperty->setAccessible(true);
                        $startProperty->setValue($reInstance, $start);
                        $endProperty = $reReflClass->getProperty($reMetadata->getEndNodeKey());
                        $endProperty->setAccessible(true);
                        $endProperty->setValue($reInstance, $end);
                        $v->add($reInstance);
                        $relIdProperty = $reReflClass->getProperty('id');
                        $relIdProperty->setAccessible(true);
                        $relIdProperty->setValue($reInstance, $relId);
                        foreach ($reMetadata->getFields() as $fkp => $field) {
                            if ($rel->hasValue($fkp)) {
                                $fp = $reReflClass->getProperty($fkp);
                                $fp->setAccessible(true);
                                $fp->setValue($reInstance, $rel->get($fkp));
                            }

                        }
                        $this->manager->getUnitOfWork()->addManagedRelationshipEntity($reInstance, $baseInstance, $key);
                    }
                    $reP = $reflClass->getProperty($key);
                    $reP->setAccessible(true);
                    $reP->setValue($baseInstance, $v);
                }
            }
        }

        return $baseInstance;
    }

    private function getHydrator($target)
    {
        return $this->manager->getRepository($target);
    }

    private function hydrateNode(Node $node, $className = null)
    {
        if ($entity = $this->manager->getUnitOfWork()->getEntityById($node->identity())) {
            return $entity;
        }
        $cl = $className !== null ? $className : $this->className;
        $reflClass = $this->getReflectionClass($cl);
        $instance = $reflClass->newInstanceWithoutConstructor();
        $cm = $this->manager->getClassMetadataFor($cl);
        foreach ($cm->getFields() as $field => $meta) {
            if ($meta instanceof Property) {
                if ($node->hasValue($field)) {
                    if ($property = $reflClass->getProperty($field)) {
                        $property->setAccessible(true);
                        $property->setValue($instance, $node->value($field));
                    }
                }
            } elseif ($meta instanceof Label) {
                $label = $meta->name;
                $v = $node->hasLabel($label);
                if ($property = $reflClass->getProperty($field)) {
                    $property->setAccessible(true);
                    $property->setValue($instance, $v);
                }
            }
        }

        foreach ($cm->getAssociations() as $key => $assoc) {
            if ($assoc->getCollection()) {
                $property = $reflClass->getProperty($key);
                $property->setAccessible(true);
                $property->setValue($instance, new ArrayCollection());
            }
        }

        foreach ($cm->getRelationshipEntities() as $key => $assoc) {
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

    private function setInversedAssociation($baseInstance, $otherInstance, $relationshipKey)
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
    private function getReflectionClass($className)
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
    private function getTargetFullClassName($className)
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
