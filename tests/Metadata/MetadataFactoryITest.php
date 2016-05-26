<?php

namespace GraphAware\Neo4j\OGM\Tests\Metadata;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\FileCacheReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use GraphAware\Neo4j\OGM\Metadata\Factory\GraphEntityMetadataFactory;
use GraphAware\Neo4j\OGM\Metadata\GraphEntityMetadata;
use GraphAware\Neo4j\OGM\Metadata\NodeEntityMetadata;
use GraphAware\Neo4j\OGM\Metadata\EntityPropertyMetadata;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Person;

/**
 * Class MetadataFactoryITest
 * @package GraphAware\Neo4j\OGM\Tests\Metadata
 *
 * @group metadata-factory-it
 */
class MetadataFactoryITest extends \PHPUnit_Framework_TestCase
{
    protected $annotationReader;

    /**
     * @var \GraphAware\Neo4j\OGM\Metadata\Factory\GraphEntityMetadataFactory
     */
    protected $entityMetadataFactory;

    public function setUp()
    {
        parent::setUp();
        $mappingDir = getenv('basedir') . DIRECTORY_SEPARATOR . 'src/Mapping/';
        AnnotationRegistry::registerFile($mappingDir.'/Neo4jOGMAnnotations.php');
        $this->annotationReader = new FileCacheReader(
            new AnnotationReader(),
            getenv('proxydir'),
            true
        );

        $this->entityMetadataFactory = new GraphEntityMetadataFactory($this->annotationReader);
    }

    public function testNodeEntityMetadataIsCreated()
    {
        $entityMetadata = $this->entityMetadataFactory->create(Person::class);
        $this->assertInstanceOf(GraphEntityMetadata::class, $entityMetadata);
        $this->assertInstanceOf(NodeEntityMetadata::class, $entityMetadata);
        $this->assertCount(2, $entityMetadata->getPropertiesMetadata());
        $this->assertInstanceOf(EntityPropertyMetadata::class, $entityMetadata->getPropertyMetadata('name'));
    }


}