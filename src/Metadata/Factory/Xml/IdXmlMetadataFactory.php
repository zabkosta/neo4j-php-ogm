<?php

namespace GraphAware\Neo4j\OGM\Metadata\Factory\Xml;

use GraphAware\Neo4j\OGM\Exception\MappingException;
use GraphAware\Neo4j\OGM\Metadata\EntityIdMetadata;
use GraphAware\Neo4j\OGM\Metadata\IdAnnotationMetadata;

class IdXmlMetadataFactory
{
    /**
     * @param \SimpleXMLElement $node
     * @param string            $className
     * @param \ReflectionClass  $reflection
     *
     * @return EntityIdMetadata
     */
    public function buildEntityIdMetadata(\SimpleXMLElement $node, $className, \ReflectionClass $reflection)
    {
        if (!isset($node->id) || !isset($node->id['name'])) {
            throw new MappingException(
                sprintf('Class "%s" OGM XML configuration has invalid or missing "id" element', $className)
            );
        }

        return new EntityIdMetadata(
            (string) $node->id['name'],
            $reflection->getProperty((string) $node->id['name']),
            new IdAnnotationMetadata()
        );
    }
}
