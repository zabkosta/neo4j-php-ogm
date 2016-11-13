<?php

namespace GraphAware\Neo4j\OGM\Tests\Proxy;

use GraphAware\Neo4j\OGM\Tests\Integration\IntegrationTestCase;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\User;

class ProxyIntegrationTest extends IntegrationTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->clearDb();
        $this->createGraph();
    }

    public function testProxyIsCreated()
    {

    }

    private function createGraph()
    {
        $user = new User('ikwattro');
        $this->em->persist($user);
        $this->em->flush();
        $this->em->clear();
    }
}