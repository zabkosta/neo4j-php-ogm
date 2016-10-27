<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\tests\Integration\UseCase\Github\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * Class WrittenIn.
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
