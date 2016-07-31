<?php

namespace GraphAware\Neo4j\OGM\Event;


use Doctrine\Common\EventArgs;
use GraphAware\Neo4j\OGM\EntityManager;

class PostFlushEventArgs extends EventArgs
{
	/**
	 * @var EntityManager
	 */
	protected $entityManager;

	/**
	 * PostFlushEventArgs constructor.
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
