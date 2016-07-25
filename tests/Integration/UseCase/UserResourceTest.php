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

        $this->assertGraphExist('(r:Resource {name:"wood"})<-[:HAS_RESOURCE {amount:20}]-(u:User {login:"ikwattro"})-[:HAS_ROLE]->(role:SecurityRole {name:"view_pages"})');
    }

    private function init()
    {
        // Setup initial graph
        $this->clearDb();
        $user = new User('ikwattro');
        $role = new SecurityRole('view_pages');
        $resource = new ResourceModel('wood');
        $user->addRole($role);
        $this->em->persist($user);
        $this->em->persist($resource);
        $this->em->flush();
        $this->em->clear();
    }
}