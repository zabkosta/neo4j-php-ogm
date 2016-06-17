<?php

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