<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\UseCase\SimpleBlog;

use GraphAware\Neo4j\OGM\Tests\Integration\IntegrationTestCase;
use GraphAware\Neo4j\OGM\Tests\Integration\UseCase\SimpleBlog\Model\SimpleBlogUser;

class SimpleBlogIntegrationTest extends IntegrationTestCase
{
    public function testInversedSideRelationshipEntityHydratesWhenNotCollection()
    {
        $this->init();
        /** @var SimpleBlogUser $user */
        $user = $this->em->getRepository(SimpleBlogUser::class)->findOneBy('name', 'john');
        $publications = $user->getPosts();
        foreach ($publications as $publication) {
            echo $publication->getPost()->getPublication()->getUser()->getName() . PHP_EOL;
        }
    }

    private function init()
    {
        $this->clearDb();
        $user = new SimpleBlogUser('john');
        $user->createPost('New Blog Post');
        $this->em->persist($user);
        $this->em->flush();
        $this->em->clear();
    }
}