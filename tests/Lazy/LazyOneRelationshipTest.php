<?php

namespace GraphAware\Neo4j\OGM\Tests\Lazy;

use GraphAware\Neo4j\OGM\Tests\Integration\IntegrationTestCase;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Company;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\User;

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
}