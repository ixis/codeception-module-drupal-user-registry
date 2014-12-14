<?php

use \Codeception\Module\Drupal\UserRegistry\DrupalTestUser;
use \Codeception\Module\DrupalUserRegistry;
use \Codeception\Util\Fixtures;

/**
 * Unit tests for DrupalUserRegistry class.
 *
 * This class only covers non-API or protected/private method tests. Tests for the 'public API' methods are included
 * in DrupalUserRegistryApiTest.php
 */
class DrupalUserRegistryTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     *   Store the Actor object being used to test.
     */
    protected $tester;

    /**
     * @var \Codeception\Module\DrupalUserRegistry
     *   Store any instance of the module being tested.
     */
    protected $module;

    /**
     * Test set-up.
     */
    public function _before()
    {
        $this->module = new DrupalUserRegistry();
    }
    /**
     * Objects of this class should be instantiable.
     *
     * @test
     */
    public function shouldBeInstantiatable()
    {
        $this->assertInstanceOf('\Codeception\Module\DrupalUserRegistry', $this->module);
    }

    /**
     * This class should extend \Codeception\Module
     *
     * @test
     */
    public function shouldExtendCodeceptionModule()
    {
        $this->assertInstanceOf('\Codeception\Module', $this->module);
    }

    /**
     * Test calling manageUsers() with an empty string as $op throws the expected exception.
     *
     * @test
     */
    public function testManageUsersThrowsExceptionForEmptyOp()
    {
        $refMethod = self::getNonPublicMethod("manageTestUsers");
        $this->setExpectedException(
            '\Codeception\Exception\Module',
            "Invalid operation  when managing users."
        );
        $refMethod->invokeArgs($this->module, array(""));
    }

    /**
     * Test calling manageUsers() with an invalid $op (i.e. not "create" or "delete") throws the expected exception.
     *
     * @test
     */
    public function testManageUsersThrowsExceptionForInvalidOp()
    {
        $refMethod = self::getNonPublicMethod("manageTestUsers");
        $this->setExpectedException(
            '\Codeception\Exception\Module',
            "Invalid operation not-an-op when managing users."
        );
        $refMethod->invokeArgs($this->module, array("not-an-op"));
    }

    /**
     * Get a protected/private method of a class via ReflectionClass.
     *
     * @param string $name
     *   The name of the protected or private method.
     *
     * @return ReflectionMethod
     */
    protected static function getNonPublicMethod($name)
    {
        $class = new ReflectionClass('\Codeception\Module\DrupalUserRegistry');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}
