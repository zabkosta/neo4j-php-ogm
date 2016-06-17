<?php

/**
 * This file is part of the GraphAware Neo4j OGM package.
 *
 * (c) GraphAware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\Lazy;

use GraphAware\Neo4j\OGM\Common\Collection;

class LazyRelationshipCollection
{
    private $elements;

    private $initialized = false;

    private static $nonTriggerMethods = ['add', 'set'];

    public function __construct(array $elements = array())
    {
        $this->elements = new Collection($elements);
    }

    public function initialize()
    {
        $this->initialized = true;
    }

    public function __call($name, $args)
    {
        if (!$this->initialized && !in_array($name, self::$nonTriggerMethods)) {
            $this->initialize();
        }

        return call_user_func_array(array($this->elements, $name), $args);
    }
}