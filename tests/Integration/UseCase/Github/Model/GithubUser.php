<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use GraphAware\Neo4j\OGM\Common\Collection;

/**
 * Class GithubUser.
 *
 * @OGM\Node(label="User")
 */
class GithubUser
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
    private $login;

    /**
     * @var string
     *
     * @OGM\Property(type="string")
     */
    private $description;

    /**
     * @var GithubRepository[]|Collection
     *
     * @OGM\Relationship(targetEntity="GithubRepository", type="OWNS", direction="OUTGOING", collection=true, mappedBy="owner")
     * @OGM\Lazy()
     */
    private $ownedRepositories;

    /**
     * @var GithubRepository[]|Collection
     *
     * @OGM\Relationship(targetEntity="GithubRepository", type="STARS", direction="OUTGOING", collection=true, mappedBy="stargazers")
     * @OGM\Lazy()
     */
    private $starred;

    /**
     * @var GithubUser[]|Collection
     *
     * @OGM\Relationship(targetEntity="GithubUser", type="FOLLOWS", direction="OUTGOING", collection=true, mappedBy="followedBy")
     * @OGM\Lazy()
     */
    private $follows;

    /**
     * @var GithubUser[]|Collection
     *
     * @OGM\Relationship(targetEntity="GithubUser", type="FOLLOWS", direction="OUTGOING", collection=true, mappedBy="follows")
     * @OGM\Lazy()
     */
    private $followedBy;

    public function __construct($login)
    {
        $this->login = $login;
        $this->ownedRepositories = new Collection();
        $this->starred = new Collection();
        $this->follows = new Collection();
        $this->followedBy = new Collection();
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
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Common\Collection|\GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model\GithubRepository[]
     */
    public function getOwnedRepositories()
    {
        return $this->ownedRepositories;
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Common\Collection|\GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model\GithubRepository[]
     */
    public function getStarred()
    {
        return $this->starred;
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Common\Collection|\GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model\GithubUser[]
     */
    public function getFollows()
    {
        return $this->follows;
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Common\Collection|\GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model\GithubUser[]
     */
    public function getFollowedBy()
    {
        return $this->followedBy;
    }
}
