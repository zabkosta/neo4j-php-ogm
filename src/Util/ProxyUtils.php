<?php

namespace GraphAware\Neo4j\OGM\Util;

class ProxyUtils
{
    public static function getPropertyIdentifier(\ReflectionProperty $reflectionProperty, $className)
    {
        $key = null;
        if ($reflectionProperty->isPrivate()) {
            $key = '\\0' . $className . '\\0' . $reflectionProperty->getName();
        } else if($reflectionProperty->isProtected()) {
            $key = '' . "\0" . '*' . "\0" . $reflectionProperty->getName();
        } else if ($reflectionProperty->isPublic()) {
            $key = $reflectionProperty->getName();
        }

        if (null === $key) {
            throw new \InvalidArgumentException('Unable to detect property visibility');
        }

        return $key;
    }
}