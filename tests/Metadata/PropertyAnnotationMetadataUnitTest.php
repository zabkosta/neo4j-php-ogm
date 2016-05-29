<?php

namespace GraphAware\Neo4j\OGM\Tests\Metadata;

use GraphAware\Neo4j\OGM\Metadata\PropertyAnnotationMetadata;

/**
 * Class PropertyMetadataUnitTest
 * @package GraphAware\Neo4j\OGM\Tests\Metadata
 *
 * @group metadata
 */
class PropertyAnnotationMetadataUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        $metadata = new PropertyAnnotationMetadata('string');
        $this->assertEquals('string', $metadata->getType());
    }

    public function testIsNullableByDefault()
    {
        $metadata = new PropertyAnnotationMetadata('string');
        $this->assertTrue($metadata->isNullable());
    }

    public function testNotHaveCustomKeyByDefault()
    {
        $metadata = new PropertyAnnotationMetadata('string');
        $this->assertFalse($metadata->hasCustomKey());
    }

    public function testNotNullableCanBeDefined()
    {
        $metadata = new PropertyAnnotationMetadata('string', null, false);
        $this->assertFalse($metadata->isNullable());
    }

    public function testCustomKeyCanBePassed()
    {
        $metadata = new PropertyAnnotationMetadata('string', 'dob');
        $this->assertEquals('dob', $metadata->getKey());
    }
}