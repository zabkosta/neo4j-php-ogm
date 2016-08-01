<?php

namespace GraphAware\Neo4j\OGM\Event;


use Doctrine\Common\EventArgs;
use GraphAware\Neo4j\OGM\EntityManager;

class PreFlushEventArgs extends EventArgs
{
	/**
	 * @var EntityManager
	 */
	protected $entityManager;

	/**
	 * PreFlushEventArgs constructor.
	 *
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * @return EntityManager
	 */
	public function getEntityManager()
	{
		return $this->entityManager;
	}
}
