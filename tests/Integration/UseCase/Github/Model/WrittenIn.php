<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * Class WrittenIn
 * @package GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model
 *
 * @OGM\RelationshipEntity(type="WRITTEN_IN")
 */
class WrittenIn
{
    /**
     * @var int
     *
     * @OGM\GraphId()
     */
    private $id;

    /**
     * @var GithubRepository
     *
     * @OGM\StartNode(targetEntity="GithubRepository")
     *
     */
    private $repository;

    /**
     * @var Language
     *
     * @OGM\EndNode(targetEntity="Language")
     */
    private $language;

    /**
     * @var int
     *
     * @OGM\Property(type="int")
     */
    private $linesOfCode;

    public function __construct(GithubRepository $repository, Language $language, $linesOfCode)
    {
        $this->repository = $repository;
        $this->language = $language;
        $this->linesOfCode = $linesOfCode;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model\GithubRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model\Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return int
     */
    public function getLinesOfCode()
    {
        return $this->linesOfCode;
    }

}