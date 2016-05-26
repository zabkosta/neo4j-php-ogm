<?php

namespace GraphAware\Neo4j\OGM\Tests\Metadata;

use GraphAware\Neo4j\OGM\Metadata\PropertyMetadata;

/**
 * Class PropertyMetadataUnitTest
 * @package GraphAware\Neo4j\OGM\Tests\Metadata
 *
 * @group metadata
 */
class PropertyMetadataUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        $metadata = new PropertyMetadata('string');
        $this->assertEquals('string', $metadata->getType());
    }

    public function testIsNullableByDefault()
    {
        $metadata = new PropertyMetadata('string');
        $this->assertTrue($metadata->isNullable());
    }

    public function testNotHaveCustomKeyByDefault()
    {
        $metadata = new PropertyMetadata('string');
        $this->assertFalse($metadata->hasCustomKey());
    }

    public function testNotNullableCanBeDefined()
    {
        $metadata = new PropertyMetadata('string', null, false);
        $this->assertFalse($metadata->isNullable());
    }

    public function testCustomKeyCanBePassed()
    {
        $metadata = new PropertyMetadata('string', 'dob');
        $this->assertEquals('dob', $metadata->getKey());
    }
}