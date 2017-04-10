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

interface TypeMapping
{
    /**
     * @return string The name of the custom type mapper
     */
    public function getName();

    /**
     * @param  mixed $value
     * @return mixed The converted value
     */
    public function convertToPHPValue($value);

    /**
     * @param  mixed $value
     * @return mixed a single or an array of graph entity property converted values
     */
    public function convertToDatabaseValue($value);
}