<?php

namespace GraphAware\Neo4j\OGM\Tests\Lazy;

use GraphAware\Neo4j\OGM\Tests\Integration\IntegrationTestCase;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Company;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\User;
use GraphAware\Neo4j\OGM\Tests\Lazy\Model\MetaResource;
use GraphAware\Neo4j\OGM\Tests\Lazy\Model\Resources;
use GraphAware\Neo4j\OGM\Tests\Lazy\Model\User as LazyUser;


/**
 * Class LazyOneRelationshipTest
 * @package GraphAware\Neo4j\OGM\Tests\Lazy
 *
 * @group lazy-one
 */
class LazyOneRelationshipTest extends IntegrationTestCase
{
    public function testCascadeHydrationForSimpleRelationshipsOnEndNodes()
    {
        $this->clearDb();
        $user1 = new User('user1');
        $user2 = new User('user2');
        $company = new Company('Acme');
        $user2->setCurrentCompany($company);
        $company->addEmployee($user2);
        $user1->addLoves($user2);
        $this->em->persist($user1);
        $this->em->persist($user2);
        $this->em->flush();
        $this->em->clear();

        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy('login', 'user1');
        $this->assertCount(1, $user->getLoves());
        $friend = $user->getLoves()[0];
        $comp = $friend->getCurrentCompany();
        $this->assertEquals('Acme', $comp->getName());
    }

    /**
     * @group lazy-joran
     */
    public function testCascadeTraversals()
    {
        $this->clearDb();
        $meta = new MetaResource('wood');
        $this->em->persist($meta);
        $this->em->flush();
        $this->em->clear();

        $metaResource = $this->em->getRepository(MetaResource::class)->findOneBy('resourceType', 'wood');
        $rWood = new Resources($metaResource);
        $rWood->setResourceCount(20);
        $user = new LazyUser();
        $this->em->persist($user);
        $this->em->persist($rWood);
        $this->em->flush();
        $user->addResource($rWood);
        $this->em->persist($user);
        $this->em->flush();

        $this->em->clear();

        $users = $this->em->getRepository(LazyUser::class)->findAll();
        foreach ($users as $user) {
            foreach ($user->getResources() as $resource) {
                $this->assertNotNull($resource);
                $this->assertInstanceOf(MetaResource::class, $resource->getMetaResource());
            }
        }
    }
}