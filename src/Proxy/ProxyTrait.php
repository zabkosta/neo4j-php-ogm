<?php
/**
 * User: zkosta
 * Date: 2/3/19
 * Time: 7:23 PM
 */

namespace GraphAware\Neo4j\OGM\Proxy;

trait ProxyTrait
{
    private $em;
    private $initialized = [];
    private $initializers = [];
    private $node;

        public function __setNode($node)
        {
            $this->node = $node;
        }

        public function __setInitializers(array $initializers)
        {
            $this->initializers = $initializers;
        }

        public function __setInitialized($property)
        {
            $this->initialized[$property] = null;
        }

        public function __initializeProperty($propertyName)
        {
            if (!array_key_exists($propertyName, $this->initialized)) {
            $this->initializers[$propertyName]->initialize($this->node, $this);
                $this->initialized[$propertyName] = null;
            }
        }
}