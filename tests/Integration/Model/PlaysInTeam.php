<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * Class PlaysInTeam
 * @package GraphAware\Neo4j\OGM\Tests\Integration\Model
 *
 * @OGM\RelationshipEntity(type="PLAYS_IN_TEAM")
 */
class PlaysInTeam
{
    /**
     * @var int
     *
     * @OGM\GraphId()
     */
    protected $id;

    /**
     * @var Player
     *
     * @OGM\StartNode(targetEntity="Player")
     */
    protected $player;

    /**
     * @var Team
     *
     * @OGM\EndNode(targetEntity="Team")
     */
    protected $team;

    /**
     * @var int
     *
     * @OGM\Property(type="int")
     */
    protected $since;

    public function __construct(Player $player, Team $team, $since)
    {
        $this->player = $player;
        $this->team = $team;
        $this->since = $since;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Tests\Integration\Model\Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Tests\Integration\Model\Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @return int
     */
    public function getSince()
    {
        return $this->since;
    }


}