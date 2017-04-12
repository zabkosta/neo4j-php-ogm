<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\Converters;

use GraphAware\Neo4j\OGM\Exception\ConverterException;

class TimestampConverter extends Converter
{
    public function getName()
    {
        return 'timestamp';
    }

    public function toDatabaseValue($value, array $options)
    {
        if (null === $value) {
            return $value;
        }

        if ($value instanceof \DateTime) {
            $timestamp = $value->getTimestamp();
            $multiplier = array_key_exists('db_type', $options) && 'long' === $options['db_type'] ? 1000 : 1;

            return $timestamp * $multiplier;
        }

        throw new ConverterException(sprintf('Unable to convert value in converter "%s"', $this->getName()));
    }

    public function toPHPValue(array $values, array $options)
    {
        if (!isset($values[$this->propertyName]) || null === $values[$this->propertyName]) {
            return null;
        }

        $isLong = isset($options['db_type']) && 'long' === $options['db_type'];
        $divider = $isLong ? 1000 : 1;

        if (isset($options['php_type']) && 'datetime' === $options['php_type']) {
            $v = $values[$this->propertyName] / $divider;
            $dt = new \DateTime();
            $dt->setTimestamp($v);

            return $dt;
        }

        return $values[$this->propertyName] / $divider;
    }

}