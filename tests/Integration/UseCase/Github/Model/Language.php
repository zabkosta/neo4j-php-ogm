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
use GraphAware\Neo4j\OGM\Common\Collection;

/**
 * Class Language.
 *
 * @OGM\Node(label="Language")
 */
class Language
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
     * @var WrittenIn[]
     *
     * @OGM\Relationship(relationshipEntity="WrittenIn", direction="INCOMING", collection=true, mappedBy="language")
     * @OGM\Lazy()
     */
    private $repositories;

    public function __construct($name)
    {
        $this->name = $name;
        $this->repositories = new Collection();
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
     * @return \GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model\WrittenIn[]
     */
    public function getRepositories()
    {
        return $this->repositories;
    }
}
