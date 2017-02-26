<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration;

use GraphAware\Neo4j\OGM\Tests\Integration\Models\BooleanLabel\BlogPost;

/**
 * Class BooleanLabelTest
 * @package GraphAware\Neo4j\OGM\Tests\Integration
 *
 * @group label-it
 */
class BooleanLabelTest extends IntegrationTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->clearDb();
    }

    public function testBlogPostIsUnpublishedByDefault()
    {
        $blogpost = new BlogPost('Learn X');
        $this->persist($blogpost);
        $this->em->flush();
        $this->assertGraphExist('(b:BlogPost {title:"Learn X"})');
        $this->assertGraphNotExist('(b:Published)');
    }

    public function testBlogPostIsPublishedIfLabelSetOnCreate()
    {
        $blogpost = new BlogPost('Learn X');
        $blogpost->setPublished(true);
        $this->persist($blogpost);
        $this->em->flush();
        $this->assertGraphExist('(b:BlogPost:Published {title:"Learn X"})');
    }

    public function testBlogPostIsNotPublishedOnLoad()
    {
        $blogpost = new BlogPost('Learn X');
        $this->persist($blogpost);
        $this->em->flush();
        $this->em->clear();

        /** @var BlogPost $post */
        $post = $this->em->getRepository(BlogPost::class)->findAll()[0];
        $this->assertTrue($post->getPublished() === null || $post->getPublished() === false);
    }

    public function testBlogPostIsPublishedOnLoad()
    {
        $blogpost = new BlogPost('Learn X');
        $blogpost->setPublished(true);
        $this->persist($blogpost);
        $this->em->flush();
        $this->em->clear();

        /** @var BlogPost $post */
        $post = $this->em->getRepository(BlogPost::class)->findAll()[0];
        $this->assertTrue($post->getPublished());
    }
}