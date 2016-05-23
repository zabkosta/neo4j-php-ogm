<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration;

use GraphAware\Neo4j\Client\ClientBuilder;
use GraphAware\Neo4j\OGM\Manager;

class IntegrationTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \GraphAware\Neo4j\Client\Client
     */
    protected $client;

    /**
     * @var \GraphAware\Neo4j\OGM\Manager
     */
    protected $em;

    public function setUp()
    {
        $this->client = ClientBuilder::create()
            ->addConnection('default', 'http://localhost:7474')
            ->build();

        $this->em = new Manager($this->client);
    }

    public function clearDb()
    {
        $this->client->run('MATCH (n) DETACH DELETE n');
    }

    public function resetEm()
    {
        $this->em->clear();
    }

    protected function assertGraphNotExist($q)
    {
        $this->assertTrue($this->checkGraph($q)->size() < 1);
    }

    protected function assertGraphExist($q)
    {
        $this->assertTrue($this->checkGraph($q)->size() > 0);
    }

    protected function checkGraph($q)
    {
        return $this->client->run('MATCH ' . $q . ' RETURN *');
    }
}