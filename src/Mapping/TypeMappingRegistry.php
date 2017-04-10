<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\Tests\Mapping;

final class TypeMappingRegistry
{
    /**
     * @var array|string[]
     */
    protected $typeMappings = [];

    /**
     * @var array|TypeMapping[]
     */
    protected $typeObjects = [];

    public function register($name, $class)
    {
        if (array_key_exists($name, $this->typeMappings)) {
            throw new \InvalidArgumentException(sprintf('A Type Mapping already exist for name "%s"', $name));
        }
    }

    /**
     * @param $name
     * @return TypeMapping
     * @throws \InvalidArgumentException If no valid TypeMapping is found for the given name
     */
    public function getType($name)
    {
        if (!isset($this->typeObjects[$name])) {
            if (!isset($this->typeMappings[$name])) {
                throw new \InvalidArgumentException(sprintf('No Type Mapping registered for type "%s"', $name));
            }

            $this->typeObjects[$name] = new $this->typeMappings[$name]();
        }

        return $this->typeObjects[$name];
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasType($name)
    {
        return array_key_exists($name, $this->typeMappings);
    }
}