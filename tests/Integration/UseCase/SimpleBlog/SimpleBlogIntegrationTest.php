<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\UseCase\SimpleBlog;

use GraphAware\Neo4j\OGM\Tests\Integration\IntegrationTestCase;
use GraphAware\Neo4j\OGM\Tests\Integration\UseCase\SimpleBlog\Model\SimpleBlogPost;
use GraphAware\Neo4j\OGM\Tests\Integration\UseCase\SimpleBlog\Model\SimpleBlogUser;

class SimpleBlogIntegrationTest extends IntegrationTestCase
{
    public function testInversedSideRelationshipEntityHydratesWhenNotCollection()
    {
        $this->init();
        /** @var SimpleBlogUser $user */
        $user = $this->em->getRepository(SimpleBlogUser::class)->findOneBy('name', 'john');
        $name = $user->getName();
        $this->assertEquals($name, $user->getPosts()[0]->getPost()->getPublication()->getUser()->getName());
    }

    public function testInversedSideHydrationWhileLoadedSeparately()
    {
        $this->init();
        /** @var SimpleBlogUser $user */
        $user = $this->em->getRepository(SimpleBlogUser::class)->findOneBy('name', 'john');
        /** @var SimpleBlogPost $post */
        $post = $this->em->getRepository(SimpleBlogPost::class)->findOneBy('title', 'New Blog Post');
        $this->assertEquals(spl_object_hash($user), spl_object_hash($post->getPublication()->getUser()));
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