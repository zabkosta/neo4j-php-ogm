<?php

namespace GraphAware\Neo4j\OGM\Tests\Proxy;

use GraphAware\Neo4j\OGM\Proxy\ProxyFactory;
use GraphAware\Neo4j\OGM\Tests\Integration\IntegrationTestCase;
use GraphAware\Neo4j\OGM\Tests\Util\NodeProxy;
use GraphAware\Neo4j\OGM\Proxy\EntityProxy;

class ProxyTest extends IntegrationTestCase
{
    public function testProxyCreation()
    {
        $cm = $this->em->getClassMetadata(Init::class);
        $factory = new ProxyFactory($this->em, $cm);
        $id = $this->createSmallGraph();
        $o = $factory->fromNode(new NodeProxy($id));

        $this->assertInstanceOf(Init::class, $o);
        $this->assertInstanceOf(EntityProxy::class, $o);
        $this->assertInstanceOf(Related::class, $o->getRelation());
        $this->assertNotNull($o->getRelation()->getId());
    }

    private function createSmallGraph()
    {
        return $this->client->run('CREATE (n:Init)-[:RELATES]->(n2:Related) RETURN id(n) AS id')->firstRecord()->get('id');
    }
}