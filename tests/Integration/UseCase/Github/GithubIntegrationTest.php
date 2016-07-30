<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github;

use GraphAware\Neo4j\OGM\Lazy\LazyRelationshipCollection;
use GraphAware\Neo4j\OGM\Tests\Integration\IntegrationTestCase;
use GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model\GithubRepository;
use GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model\GithubUser;

class GithubIntegrationTest extends IntegrationTestCase
{
    public function testSimpleUserIsSaved()
    {
        $this->clearDb();
        $user = new GithubUser('ikwattro');
        $user->setDescription("neo4j consultant");
        $this->em->persist($user);
        $this->em->flush();

        $this->assertGraphExist('(u:User {login:"ikwattro", description:"neo4j consultant"})');
    }

    public function testSimpleUserIsSavedAndUpdatedPropertiesAreUpdated()
    {
        $this->clearDb();
        $user = new GithubUser('ikwattro');
        $user->setDescription("neo4j consultant");
        $this->em->persist($user);
        $this->em->flush();
        $this->assertGraphExist('(u:User {login:"ikwattro", description:"neo4j consultant"})');
        $user->setDescription("neo4j developer");
        $this->em->flush();
        $this->assertGraphExist('(u:User {login:"ikwattro", description:"neo4j developer"})');
    }

    public function testUserIsFetchedFromDatabaseAndUpdated()
    {
        $this->clearDb();
        $user = new GithubUser('ikwattro');
        $user->setDescription("neo4j consultant");
        $this->em->persist($user);
        $this->em->flush();
        $this->assertGraphExist('(u:User {login:"ikwattro", description:"neo4j consultant"})');
        $this->em->clear();

        /** @var GithubUser $ikwattro */
        $ikwattro = $this->em->getRepository(GithubUser::class)->findOneBy('login', 'ikwattro');
        $this->assertEquals('ikwattro', $ikwattro->getLogin());
        $this->assertEquals('neo4j consultant', $ikwattro->getDescription());
        $this->assertTrue($ikwattro->getOwnedRepositories() instanceof LazyRelationshipCollection);
        $ikwattro->setDescription("neo4j developer");
        $this->em->flush();
        $this->assertGraphExist('(u:User {login:"ikwattro", description:"neo4j developer"})');
    }

    public function testUserAssignedRepositoryWorkflow()
    {
        $this->clearDb();
        $user = $this->createUser('ikwattro');
        $repo = new GithubRepository('neo4j-reco', $user);
        $user->getOwnedRepositories()->add($repo);
        $this->em->flush();
        $this->assertGraphExist('(u:User {login:"ikwattro"})-[:OWNS]->(r:Repository {name:"neo4j-reco"})');
        $this->em->clear();

        /** @var GithubUser $ikwattro */
        $ikwattro = $this->em->getRepository(GithubUser::class)->findOneBy('login', 'ikwattro');
        $this->assertTrue($ikwattro->getOwnedRepositories()->first() instanceof GithubRepository);
        $this->assertEquals($ikwattro->getLogin(), $ikwattro->getOwnedRepositories()[0]->getOwner()->getLogin());
    }

    /**
     * @param $login
     * @return \GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model\GithubUser
     */
    private function createUser($login)
    {
        $user = new GithubUser($login);
        $user->setDescription("neo4j consultant");
        $this->em->persist($user);
        $this->em->flush();
        $this->assertGraphExist('(u:User {login:"'.$login.'", description:"neo4j consultant"})');

        return $user;
    }
}