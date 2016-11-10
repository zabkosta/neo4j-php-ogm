<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\Tests\Performance;

use GraphAware\Neo4j\OGM\Tests\Integration\IntegrationTestCase;
use GraphAware\Neo4j\OGM\Tests\Performance\Domain\Person;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Class NodeWithRelsPerformanceTest.
 *
 * @group perf-test
 */
class NodeWithRelsPerformanceTest extends IntegrationTestCase
{
    private static $NUMBER_OF_RUNS = 10;

    /**
     * @var \Symfony\Component\Stopwatch\Stopwatch
     */
    protected $stopwatch;

    public function setUp()
    {
        parent::setUp();
        $this->stopwatch = new Stopwatch();
    }

    /**
     * @group perf-test-1
     */
    public function testPerformances1()
    {
        $this->clearDb();
        $this->load1000PersonsWith3000RelsDepth1();
        $personsRepository = $this->em->getRepository(Person::class);
        $avgTime = 0;
        for ($i = 1; $i < self::$NUMBER_OF_RUNS; ++$i) {
            $testTag = 'run'.$i;
            $this->stopwatch->start($testTag);
            $persons = $personsRepository->findAll();
            $this->assertCount(1000, $persons);
            $e = $this->stopwatch->stop($testTag);
            $avgTime += $e->getDuration();
        }

        $this->displayMessage(1000, 3000, $avgTime / self::$NUMBER_OF_RUNS);
    }

    /**
     * @group perf-test-2
     */
    public function testPerformances2()
    {
        $this->clearDb();
        $this->load2000PersonsWith5SkillsDepth1();
        $personsRepository = $this->em->getRepository(Person::class);
        $avgTime = 0;
        for ($i = 1; $i < self::$NUMBER_OF_RUNS; ++$i) {
            $testTag = 'run'.$i;
            $this->stopwatch->start($testTag);
            $persons = $personsRepository->findAll();
            $this->assertCount(2000, $persons);
            $e = $this->stopwatch->stop($testTag);
            $avgTime += $e->getDuration();
        }

        $this->displayMessage(1000, 3000, $avgTime / self::$NUMBER_OF_RUNS);
    }

    private function displayMessage($numberOfEntities, $numberOfRels, $time)
    {
        echo PHP_EOL.sprintf('%d entities with %d relationships fetched and hydrated in %f ms', $numberOfEntities, $numberOfRels, $time).PHP_EOL;
    }

    private function load2000PersonsWith5SkillsDepth1()
    {
        $query = $this->getQuery(2000, 5);
        $this->client->run($query);
        $this->assertGraphState(2000, 1000, 1000);
    }

    private function load1000PersonsWith3000RelsDepth1()
    {
        $query = $this->getQuery(1000, 3);
        $this->client->run($query);
        $this->assertGraphState(1000, 500, 500);
    }

    private function assertGraphState($numberOfPersons, $numberOfSkills, $numberOfCompanies)
    {
        $personsCount = $this->client->run('MATCH (n:Person) RETURN count(*) as c')->firstRecord()->get('c');
        $skillsCount = $this->client->run('MATCH (n:Skill) RETURN count(*) as c')->firstRecord()->get('c');
        $companiesCount = $this->client->run('MATCH (n:Company) RETURN count(*) as c')->firstRecord()->get('c');
        $this->assertEquals($numberOfPersons, $personsCount);
        $this->assertEquals($numberOfSkills, $skillsCount);
        $this->assertEquals($numberOfCompanies, $companiesCount);
    }

    private function getQuery($numberOfPersons, $maxSkillsPerPerson)
    {
        $query = "
        CALL generate.nodes('Person', '{firstName: firstName, lastName: lastName, email: email, accountBalance: randomNumber}', " .$numberOfPersons.")
        YIELD nodes as persons
		CALL generate.nodes('Skill', '{name: word, averageLevel: randomNumber}', " .round($numberOfPersons / 2).")
		YIELD nodes as skills
		CALL generate.nodes('Company', '{name: companyName}', " .round($numberOfPersons / 2).")
		YIELD nodes as companies
		CALL generate.relationships(persons, skills, 'HAS_SKILL', '', " .$numberOfPersons.' , '.$maxSkillsPerPerson.")
		YIELD relationships as skillRels
		CALL generate.relationships(persons, companies, 'WORKS_AT', '', " .$numberOfPersons." , '1')
		YIELD relationships as relationships RETURN * ";

        return $query;
    }
}
