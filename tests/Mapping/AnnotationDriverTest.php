<?php

namespace GraphAware\Neo4j\OGM\Tests\Mapping;

use GraphAware\Neo4j\OGM\Mapping\AnnotationDriver;
use GraphAware\Neo4j\OGM\Tests\Mapping\NodeEntityWithCustomRepo;

class AnnotationDriverTest extends \PHPUnit_Framework_TestCase
{
    public function testCustomRepositoryClassIsParsed()
    {
        $driver = new AnnotationDriver();
        $metadata = $driver->readAnnotations(NodeEntityWithCustomRepo::class);
        $this->assertArrayHasKey('repository', $metadata);
    }
}