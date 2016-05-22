<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\NativeQuery;
use GraphAware\Neo4j\OGM\Tests\Integration\IntegrationTestCase;
use GraphAware\Neo4j\OGM\Tests\Integration\NativeQuery\Model\NewsFeed;
use GraphAware\Neo4j\OGM\Tests\Integration\NativeQuery\Model\Post;
use GraphAware\Neo4j\OGM\Tests\Integration\NativeQuery\Model\PostRepository;

/**
 * @group query-result-it
 */
class QueryResultMappingITTest extends IntegrationTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->clearDb();
    }

    public function testNativeQueryResultMapping()
    {
        $this->createGraph();
        /** @var PostRepository $repository */
        $repository = $this->em->getRepository(Post::class);
        $this->assertInstanceOf(PostRepository::class, $repository);

        /** @var NewsFeed[] $result */
        $feeds = $repository->getNewsFeed();
        foreach ($feeds as $feed) {
            $this->assertInstanceOf(NewsFeed::class, $feed);
        }
        $this->assertEquals('Graph Aided Search', $feeds[0]->getPost()->getTitle());
    }

    private function createGraph()
    {
        $query = 'CREATE (p:Post {title:"Graph Aided Search"}), (p2:Post {title:"Use SDN4 like a superhero!"})';
        $this->client->run($query);
    }
}