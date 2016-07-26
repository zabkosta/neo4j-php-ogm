<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\UseCase;

use GraphAware\Neo4j\OGM\Tests\Integration\IntegrationTestCase;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Resource as ResourceModel;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\SecurityRole;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\User;

class UserResourceTest extends IntegrationTestCase
{
    public function testFetchExistingUserWithRoleAndAddResource()
    {
        $this->init();
        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy('login', 'ikwattro');
        /** @var ResourceModel $resource */
        $resource = $this->em->getRepository(ResourceModel::class)->findOneBy('name', 'wood');
        $this->assertNotNull($user);
        $this->assertNotNull($resource);
        $user->addResource($resource, 20);
        $this->em->flush();
        foreach (['water','sun','coat', 'stone'] as $res) {
            $res2 = $this->em->getRepository(ResourceModel::class)->findOneBy('name', $res);
            $user->addResource($res2, 15);
        }
        $this->em->flush();



        $this->assertGraphExist('(r:Resource {name:"wood"})<-[:HAS_RESOURCE {amount:20}]-(u:User {login:"ikwattro"})-[:HAS_ROLE]->(role:SecurityRole {name:"view_pages"})');
        $result = $this->client->run('MATCH (n:User {login:"ikwattro"}) RETURN size((n)-[:HAS_RESOURCE]->()) AS value');
        $resourcesCount = $result->firstRecord()->get('value');
        $this->assertEquals(5, $resourcesCount);
    }

    private function init()
    {
        // Setup initial graph
        $this->clearDb();
        $user = new User('ikwattro');
        $role = new SecurityRole('view_pages');
        foreach (['wood', 'stone', 'water', 'sun', 'coat'] as $res) {
            $resource = new ResourceModel($res);
            $this->em->persist($resource);
        }
        $user->addRole($role);
        $this->em->persist($user);
        $this->em->flush();
        $this->em->clear();
    }
}