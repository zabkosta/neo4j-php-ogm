<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use GraphAware\Neo4j\OGM\Common\Collection;


/**
 * Class Team
 * @package GraphAware\Neo4j\OGM\Tests\Integration\Model
 *
 * @OGM\Node(label="Team")
 */
class Team
{
    /**
     * @OGM\GraphId()
     */
    protected $id;

    /**
     * @OGM\Property(type="string")
     * @var string
     */
    protected $name;

    /**
     * @var PlaysInTeam[]|Collection
     *
     * @OGM\Relationship(relationshipEntity="PlaysInTeam", direction="INCOMING", collection=true)
     * @OGM\OrderBy(property="player.name", order="ASC")
     */
    protected $memberships;

    public function __construct($name)
    {
        $this->name = $name;
        $this->memberships = new Collection();
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
     * @return \GraphAware\Neo4j\OGM\Common\Collection|\GraphAware\Neo4j\OGM\Tests\Integration\Model\PlaysInTeam[]
     */
    public function getMemberships()
    {
        return $this->memberships;
    }

    public function addMembership(PlaysInTeam $playsInTeam)
    {
        if (!$this->memberships->contains($playsInTeam)) {
            $this->memberships->add($playsInTeam);
        }
    }



}