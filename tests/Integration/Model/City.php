<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use GraphAware\Neo4j\OGM\Common\Collection;

/**
 * Class City
 * @package GraphAware\Neo4j\OGM\Tests\Integration\Model
 *
 * @OGM\Node(label="City")
 */
class City
{
    /**
     * @OGM\GraphId()
     */
    protected $id;

    /**
     * @OGM\Property(type="string")
     */
    protected $name;

    /**
     * @OGM\Relationship(relationshipEntity="LivesIn", direction="INCOMING", collection=true)
     * @OGM\Lazy()
     * @OGM\OrderBy(property="since", order="DESC")
     */
    protected $habitants;

    public function __construct($name)
    {
        $this->name = $name;
        $this->habitants = new Collection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getHabitants()
    {
        return $this->habitants;
    }

    public function addHabitant(LivesIn $livesIn)
    {
        $this->habitants->add($livesIn);
    }
}