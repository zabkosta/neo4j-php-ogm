<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use GraphAware\Neo4j\OGM\Common\Collection;

/**
 * Class GithubRepository
 * @package GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model
 *
 * @OGM\Node(label="Repository")
 */
class GithubRepository
{
    /**
     * @var int
     *
     * @OGM\GraphId()
     */
    private $id;

    /**
     * @var string
     *
     * @OGM\Property(type="string")
     */
    private $name;

    /**
     * @var GithubUser
     *
     * @OGM\Relationship(targetEntity="GithubUser", type="OWNS", direction="INCOMING", mappedBy="ownedRepositories")
     */
    private $owner;

    /**
     * @var WrittenIn[]
     *
     * @OGM\Relationship(relationshipEntity="WrittenIn", collection=true, direction="OUTGOING", mappedBy="repository")
     */
    private $writtenLanguages;

    public function __construct($name, GithubUser $owner = null)
    {
        $this->name = $name;
        if (null !== $owner) {
            $this->owner = $owner;
        }
        $this->writtenLanguages = new Collection();
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
     * @return \GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model\GithubUser
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model\WrittenIn[]
     */
    public function getWrittenLanguages()
    {
        return $this->writtenLanguages;
    }
}