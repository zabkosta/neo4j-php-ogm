<?php

namespace Movies;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @OGM\Node(label="Person")
 */
class Person
{
    /**
     * @OGM\GraphId()
     * @var int
     */
    protected $id;

    /**
     * @OGM\Property(type="string")
     * @var string
     */
    protected $name;

    /**
     * @OGM\Property(type="born")
     * @var int
     */
    protected $born;

    /**
     * Person constructor.
     * @param string $name
     * @param int|null $born
     */
    public function __construct($name, $born = null)
    {
        $this->name = $name;
        $this->born = $born;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getBorn()
    {
        return $this->born;
    }

    /**
     * @param int $year
     */
    public function setBorn($year)
    {
        $this->born = $year;
    }
}