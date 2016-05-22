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

    /**
     * @group rel-ref
     */
    public function testRelatedEntitiesCanBeRemoved()
    {
        $user = new User('ikwattro');
        $user2 = new User('alenegro81');
        $user3 = new User('jexp');
        $user->getFriends()->add($user2);
        $user->getFriends()->add($user3);
        $this->em->persist($user);
        $this->em->flush();
        $this->assertGraphExist('(u:User {login:"ikwattro"})-[r:FOLLOWS]->(o:User {login:"alenegro81"})');
        $this->assertGraphExist('(u:User {login:"ikwattro"})-[r:FOLLOWS]->(o:User {login:"jexp"})');
        $user->getFriends()->removeElement($user2);
        $this->em->persist($user);
        $this->em->flush();
        $this->assertGraphNotExist('(u:User {login:"ikwattro"})-[r:FOLLOWS]->(o:User {login:"alenegro81"})');
    }

    private function assertGraphNotExist($q)
    {
        $this->assertTrue($this->checkGraph($q)->size() < 1);
    }

    private function assertGraphExist($q)
    {
        $this->assertTrue($this->checkGraph($q)->size() > 0);
    }

    private function checkGraph($q)
    {
        return $this->client->run('MATCH ' . $q . ' RETURN *');
    }
}