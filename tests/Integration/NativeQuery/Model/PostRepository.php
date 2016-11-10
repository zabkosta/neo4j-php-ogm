<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
