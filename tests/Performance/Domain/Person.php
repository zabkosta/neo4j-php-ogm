<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\Tests\Performance\Domain;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @OGM\Node(label="Person")
 */
class Person
{
    /**
     * @OGM\GraphId
     */
    protected $id;

    /**
     * @OGM\Property(type="string")
     */
    protected $firstName;

    /**
     * @OGM\Property(type="string")
     */
    protected $lastName;

    /**
     * @OGM\Property(type="string")
     */
    protected $email;

    /**
     * @OGM\Property(type="int")
     */
    protected $accountBalance;

    /**
     * @OGM\Relationship(targetEntity="\GraphAware\Neo4j\OGM\Tests\Performance\Domain\Skill",
     *     type="HAS_SKILL", direction="OUTGOING", collection=true)
     */
    protected $skills;

    /**
     * @OGM\Relationship(targetEntity="\GraphAware\Neo4j\OGM\Tests\Performance\Domain\Company",
     *     type="WORKS_AT", direction="OUTGOING")
     */
    protected $company;

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
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getAccountBalance()
    {
        return $this->accountBalance;
    }

    /**
     * @return mixed
     */
    public function getSkills()
    {
        return $this->skills;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }
}
