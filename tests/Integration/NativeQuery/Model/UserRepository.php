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
use GraphAware\Neo4j\OGM\Tests\Integration\NativeQuery\Model\User;

class UserRepository extends BaseRepository
{
    public function findByUserNameOrEmail($username, $email)
    {
        $query = 'MATCH (user:User) WHERE user.username = {username} OR user.email = {email} RETURN user';
        $qrm = new QueryResultMapping(User::class, QueryResultMapping::RESULT_SINGLE);
    }
}
