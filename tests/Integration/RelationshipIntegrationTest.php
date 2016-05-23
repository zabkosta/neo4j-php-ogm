<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration;

use GraphAware\Neo4j\OGM\Tests\Integration\Model\Company;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\User;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Movie;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Person;

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
        $this->em->clear();
        $ikwattro = $this->em->getRepository(User::class)->findOneBy('login', 'ikwattro');
        $this->assertCount(1, $ikwattro->getFriends());
    }

    /**
     * @group rel-ref
     */
    public function testRelatedEntitiesFetchedCanBeRemoved()
    {
        $user = new User('ikwattro');
        $user2 = new User('jexp');
        $user3 = new User('michal');
        $user->getFriends()->add($user2);
        $user->getFriends()->add($user3);
        $this->em->persist($user);
        $this->em->flush();
        $this->assertGraphExist('(u:User {login:"ikwattro"})-[r:FOLLOWS]->(o:User {login:"michal"})');
        $this->assertGraphExist('(u:User {login:"ikwattro"})-[r:FOLLOWS]->(o:User {login:"jexp"})');
        $this->em->clear();
        /** @var User $ikwattro */
        $ikwattro = $this->em->getRepository(User::class)->findOneBy('login', 'ikwattro');
        $this->assertInstanceOf(User::class, $ikwattro);
        $this->assertCount(2, $ikwattro->getFriends());
        foreach ($ikwattro->getFriends() as $friend) {
            if ($friend->getLogin() === 'jexp') {
                $ikwattro->getFriends()->removeElement($friend);
            }
        }
        $this->assertCount(1, $ikwattro->getFriends());
        $this->em->flush();
        $this->assertGraphNotExist('(u:User {login:"ikwattro"})-[r:FOLLOWS]->(o:User {login:"jexp"})');
    }

    /**
     * @group rel-ref-by-id
     */
    public function testRelatedEntitiesAreCorrectlyFetchedWhenRootEntityIsFoundById()
    {
        $this->playMovies();
        $id = $this->client->run('MATCH (m:Movie) WHERE m.title = "The Matrix" RETURN id(m) as id')->firstRecord()->get('id');
        /** @var Movie $movie */
        $movie = $this->em->getRepository(Movie::class)->findOneById($id);
        $this->assertEquals($id, $movie->id);
        foreach ($movie->actors as $actor) {
            $this->assertInstanceOf(Person::class, $actor);
        }
    }
}