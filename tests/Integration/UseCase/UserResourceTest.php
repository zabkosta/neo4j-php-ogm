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
        $this->assertEquals('wood', $resource->getName());
        $this->assertCount(0, $user->getUserResources());
        $user->addResource($resource, 20);
        $this->em->persist($user);
        $this->em->persist($resource);
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

    public function testUserResourcesAreNotResetWithOnlyOneResource()
    {
        $this->prepareDb();
        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy('login', 'test');
        /** @var ResourceModel $resource */
        $resource = $this->em->getRepository(ResourceModel::class)->findOneBy('name', 'wood');
        /** @var ResourceModel $resource2 */
        $resource2 = $this->em->getRepository(ResourceModel::class)->findOneBy('name', 'stone');
        $user->addResource($resource, 10);
        $user->addResource($resource2, 10);
        $this->em->flush();
        $this->em->clear();

        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy('login', 'test');
        /** @var ResourceModel $resource */
        $resource = $this->em->getRepository(ResourceModel::class)->findOneBy('name', 'wood');
        $resource->getUserResources();
        $this->assertCount(2, $user->getUserResources());
    }

    public function testFetchExistingUserWithRoleAndAddResourceWithCypherInit()
    {
        $this->prepareDb();
        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy('login', 'test');
        /** @var ResourceModel $resource */
        $resource = $this->em->getRepository(ResourceModel::class)->findOneBy('name', 'wood');
        $this->assertNotNull($user);
        $this->assertNotNull($resource);
        $this->assertEquals('wood', $resource->getName());
        $this->assertCount(0, $user->getUserResources());
        $user->addResource($resource, 20);
        $this->em->persist($user);
        $this->em->persist($resource);
        $this->em->flush();

        foreach (['water','food','work', 'stone'] as $res) {
            $res2 = $this->em->getRepository(ResourceModel::class)->findOneBy('name', $res);
            $user->addResource($res2, 15);
        }
        $this->em->flush();




        $this->assertGraphExist('(r:Resource {name:"wood"})<-[:HAS_RESOURCE]-(u:User {login:"test"})-[:HAS_ROLE]->(role:Role {name:"pageViews"})');
        $result = $this->client->run('MATCH (n:User {login:"test"}) RETURN size((n)-[:HAS_RESOURCE]->()) AS value');
        $resourcesCount = $result->firstRecord()->get('value');
        $this->assertEquals(5, $resourcesCount);
    }

    public function testRelationshipEntitiesUpdated()
    {
        $this->init();
        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy('login', 'ikwattro');
        /** @var ResourceModel $wood */
        $wood = $this->em->getRepository(ResourceModel::class)->findOneBy('name', 'wood');
        $user->addResource($wood, 10);
        /** @var ResourceModel $stone */
        $stone = $this->em->getRepository(ResourceModel::class)->findOneBy('name', 'stone');
        $user->addResource($stone, 30);
        $this->em->flush();
        $this->em->clear();

        /** @var User $me */
        $me = $this->em->getRepository(User::class)->findOneBy('login', 'ikwattro');
        $me->getUserResources()[0]->setAmount(time());
        $this->em->flush();
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

    private function prepareDb()
    {
        $em = $this->em;
        $em->getDatabaseDriver()->run("MATCH (n) DETACH DELETE n");
        $em->getDatabaseDriver()->run('CREATE (n:User {login:\'test\'})-[:HAS_ROLE]->(:Role {name:"pageViews"})');
        $em->getDatabaseDriver()->run("CREATE (r:Role{name:\"ROLE_USER\"})

create (n:Resource{name:'wood',name_DE:'Holz',icon:'fa-tree',iconColour:'#fff',colour:'#00C851 '})
create (n2:Resource{name:'stone',name_DE:'Stein',icon:'fa-cubes',iconColour:'#fff',colour:'#a1887f '})
create (n3:Resource{name:'food',name_DE:'Nahrung',icon:'fa-cutlery',iconColour:'#fff',colour:'#fb8c00 '})
create (n4:Resource{name:'water',name_DE:'Wasser',icon:'fa-tint',iconColour:'#fff',colour:'#33b5e5 '})
create (n5:Resource{name:'work',name_DE:'Arbeitskraft',icon:'fa-industry',iconColour:'#fff',colour:'#2BBBAD '})
create (n6:Resource{name:'overwatch',name_DE:'Überwachung',icon:'fa-eye',iconColour:'#fff',colour:'#aa66cc '})

create (n7:Team{name:'red_giants'})
create (n8:Team{name:'blue_dwarfs'})");
    }
}