<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\Convert;

use GraphAware\Neo4j\OGM\Tests\Integration\IntegrationTestCase;
use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 *
 * @group property-converter
 */
class TimestampConverterIntegrationTest extends IntegrationTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->clearDb();
    }

    public function testEntityCreationWithNullValueForTime()
    {
        $e = new TimestampConverterEntity();
        $this->persist($e);
        $this->em->flush();
        $this->assertGraphExist('(n:Entity)');
        $this->assertGraphNotExist('(n:Entity {time:null})');
    }

    /**
     * @group property-converter-it
     */
    public function testEntityWithDateTimeIsPersistedWithTimestampLong()
    {
        $e = new TimestampConverterEntity();
        $dt = new \DateTime("NOW");
        $e->setTime($dt);
        $this->persist($e);
        $this->em->flush();
        $ts = $dt->getTimestamp() * 1000;
        $this->assertGraphExist(sprintf('(n:Entity {time:%d})', $ts));
    }

    public function testEntityWithDateTimeIsRetrievedFromDatabase()
    {
        $e = new TimestampConverterEntity();
        $dt = new \DateTime("NOW");
        $e->setTime($dt);
        $this->persist($e);
        $this->em->flush();
        $ts = $dt->getTimestamp() * 1000;
        $this->em->clear();

        $o = $this->em->getRepository(TimestampConverterEntity::class)->findOneBy(['time' => $ts]);
        $this->assertInstanceOf(\DateTime::class, $o->getTime());
    }


}

/**
 * Class TimestampConverterEntity
 * @package GraphAware\Neo4j\OGM\Tests\Integration\Convert
 *
 * @OGM\Node(label="Entity")
 */
class TimestampConverterEntity
{
    /**
     * @var int
     *
     * @OGM\GraphId()
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @OGM\Property()
     * @OGM\Convert(type="timestamp", options={"db_type"="long","php_type"="datetime"})
     */
    protected $time;

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }
}