<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\Tests\Integration\Model;

use Doctrine\Common\Collections\ArrayCollection;
use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @OGM\Node(label="Company")
 */
class Company
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
     * @OGM\Relationship(targetEntity="User", mappedBy="currentCompany", type="WORKS_AT", direction="INCOMING", collection=true)
     * @OGM\Lazy()
     *
     * @return User[]
     */
    protected $employees;

    public function __construct($name)
    {
        $this->name = $name;
        $this->employees = new ArrayCollection();
    }

    public function addEmployee(User $user)
    {
        $this->employees[] = $user;
    }

    public function removeEmployee(User $user)
    {
        $this->employees->removeElement($user);
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
     * @return User[]
     */
    public function getEmployees()
    {
        return $this->employees;
    }
}
