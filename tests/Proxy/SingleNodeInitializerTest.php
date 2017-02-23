<?php

namespace GraphAware\Neo4j\OGM\Tests\Proxy;

use GraphAware\Neo4j\OGM\Proxy\SingleNodeInitializer;
use GraphAware\Neo4j\OGM\Tests\Integration\IntegrationTestCase;
use GraphAware\Neo4j\OGM\Tests\Util\NodeProxy;

class SingleNodeInitializerTest extends IntegrationTestCase
{
    public function testSingleNodeInitializer()
    {
        $this->clearDb();
        $startId = $this->createSmallGraph();
        $node = new NodeProxy($startId);
        $metadata = $this->em->getClassMetadata(Init::class);
        $relMeta = $metadata->getRelationships()['relation'];
        $this->assertTrue($relMeta->isLazy());

        $initializer = new SingleNodeInitializer($this->em, $relMeta, $metadata);
        $related = $initializer->initialize($node);
        $this->assertInstanceOf(Related::class, $related);
        $this->assertNotNull($related->getId());
    }

    public function testProxyIsReturnedWhenCalledFromRepository()
    {
        $this->clearDb();
        $id = $this->createSmallGraph();
        $init = $this->em->getRepository(Init::class)->findOneById($id);
        $this->assertInstanceOf(Related::class, $init->getRelation());
    }

    private function createSmallGraph()
    {
        return $this->client->run('CREATE (n:Init)-[:RELATES]->(n2:Related) RETURN id(n) AS id')->firstRecord()->get('id');
    }
}