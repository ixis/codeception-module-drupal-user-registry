<?php

namespace Codeception\Module;

/**
 * Define custom actions for unit tests.
 *
 * All non-static public methods declared in helper class will be available in $this->tester in Test files.
 */
class UnitHelper extends \Codeception\Module
{
    /**
     * Get a protected or private method of a class via ReflectionClass.
     *
     * @param string $class
     *   The fully qualified name of the class.
     * @param string $name
     *   The name of the protected or private method.
     *
     * @return \ReflectionMethod
     */
    public static function getNonPublicMethod($class, $name)
    {
        $class = new \ReflectionClass($class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}
