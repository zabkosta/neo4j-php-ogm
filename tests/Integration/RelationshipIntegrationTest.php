<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration;

use GraphAware\Neo4j\OGM\Tests\Integration\Model\Company;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\User;

/**
 * Class RelationshipIntegrationTest
 * @package GraphAware\Neo4j\OGM\Tests\Integration
 *
 * @group rel-it
 */
class RelationshipIntegrationTest extends IntegrationTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->clearDb();
    }

    public function testEntityIsPersistedWithRelationship()
    {
        $company = new Company('Acme');
        $user = new User('ikwattro');
        $user->setCurrentCompany($company);
        $company->addEmployee($user);
        $this->em->persist($user);
        $this->em->flush();
        $this->assertGraphExist('(u:User {login:"ikwattro"})-[r:WORKS_AT]->(c:Company {name:"Acme"})');
    }

    public function testEntityIsPersistedWhenOnlyOnOneSide()
    {
        $company = new Company('Acme');
        $user = new User('ikwattro');
        $company->addEmployee($user);
        $this->em->persist($company);
        $this->em->flush();
        $this->assertGraphExist('(u:User {login:"ikwattro"})-[r:WORKS_AT]->(c:Company {name:"Acme"})');
    }

    public function testRelatedEntitiesAreFetched()
    {
        $company = new Company('Acme');
        $user = new User('ikwattro');
        $company->addEmployee($user);
        $this->em->persist($company);
        $this->em->flush();
        $this->em->clear();

        $repo = $this->em->getRepository(User::class);
        $user = $repo->findOneBy('login', 'ikwattro');
        $this->assertInstanceOf(Company::class, $user->getCurrentCompany());
    }

    private function assertGraphExist($q)
    {
        $result = $this->client->run('MATCH ' . $q . ' RETURN *');
        $this->assertTrue($result->size() > 0);
    }
}