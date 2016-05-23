<?php

namespace GraphAware\Neo4j\OGM\Util;

class ClassUtils
{
    /**
     * @param string $class
     * @param string $pointOfView
     *
     * @return string
     */
    public static function getFullClassName($class, $pointOfView)
    {
        $expl = explode('\\', $class);
        if (1 === count($expl)) {
            $expl2 = explode('\\', $pointOfView);
            if (1 !== count($expl2)) {
                unset($expl2[count($expl2) - 1]);
                $class = sprintf('%s\\%s', implode('\\', $expl2), $class);
            }
        }

        if (!class_exists($class)) {
            throw new \RuntimeException(sprintf('The class "%s" could not be found', $class));
        }

        $reflClass = new \ReflectionClass($class);

        return $class;
    }
}
