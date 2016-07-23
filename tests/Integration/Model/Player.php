<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * Class Player
 *
 * @OGM\Node(label="Player")
 */
class Player
{
    /**
     * @var int
     *
     * @OGM\GraphId()
     */
    protected $id;

    /**
     * @var string
     *
     * @OGM\Property(type="string")
     */
    protected $name;

    /**
     * @var PlaysInTeam
     *
     * @OGM\Relationship(relationshipEntity="PlaysInTeam", direction="OUTGOING", mappedBy="player")
     */
    protected $playsIn;

    public function __construct($name)
    {
        $this->name = $name;
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
    public function getTeamMembership()
    {
        return $this->playsIn;
    }

    public function addToTeam(Team $team)
    {
        if (null !== $this->playsIn) {
            throw new \InvalidArgumentException('You must remove the current membership before adding a new one');
        }

        $dt = new \DateTime("NOW", new \DateTimeZone("UTC"));
        $time = $dt->getTimestamp();

        $membership = new PlaysInTeam($this, $team, $time);
        $this->playsIn = $membership;
        $team->addMembership($membership);
    }
}