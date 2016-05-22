<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\NativeQuery\Model;

use GraphAware\Neo4j\OGM\Query\QueryResultMapping;
use GraphAware\Neo4j\OGM\Repository\BaseRepository;
use GraphAware\Neo4j\OGM\Tests\Integration\NativeQuery\Model\NewsFeed;

class PostRepository extends BaseRepository
{
    public function getNewsFeed()
    {
        $qrm = new QueryResultMapping(NewsFeed::class, QueryResultMapping::RESULT_MULTIPLE);
        $query = 'MATCH (p:Post) RETURN p as post, timestamp() as timestamp';

        return $this->nativeQuery($query, null, $qrm);
    }
}