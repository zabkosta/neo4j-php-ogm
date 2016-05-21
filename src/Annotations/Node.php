<?php

namespace GraphAware\Neo4j\OGM\Annotations;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Node
{
    private static $KEY_LABEL = 'label';

    private static $KEY_REPOSITORY = 'repository';

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $repositoryClass;

    public function __construct(array $values)
    {
        $this->label = $values[self::$KEY_LABEL];
        if (array_key_exists(self::$KEY_REPOSITORY, $values)) {
            $this->repositoryClass = $values[self::$KEY_REPOSITORY];
        }
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return bool
     */
    public function hasCustomRepository()
    {
        return null !== $this->repositoryClass;
    }

    /**
     * @return string
     */
    public function getRepositoryClass()
    {
        return $this->repositoryClass;
    }
}
