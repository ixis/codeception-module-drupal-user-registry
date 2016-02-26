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
    public function instantiateClass()
    {
        $this->assertInstanceOf('\Codeception\Module\DrupalUserRegistry', $this->module);
    }

    /**
     * This class should extend \Codeception\Module
     */
    public function testIfInstanceOfClassExtendsCodeceptionModule()
    {
        $this->assertInstanceOf('\Codeception\Module', $this->module);
    }

    /**
     * Test calling manageUsers() with an empty string as $op throws the expected exception.
     */
    public function testManageUsersThrowsExceptionForEmptyOp()
    {
        $refMethod = \Codeception\Module\UnitHelper::getNonPublicMethod(
            '\Codeception\Module\DrupalUserRegistry',
            "manageTestUsers"
        );
        $this->setExpectedException(
            '\Codeception\Exception\Module',
            "Invalid operation  when managing users."
        );
        $refMethod->invokeArgs($this->module, array(""));
    }

    /**
     * Test calling manageUsers() with an invalid $op (i.e. not "create" or "delete") throws the expected exception.
     */
    public function testManageUsersThrowsExceptionForInvalidOp()
    {
        $refMethod = \Codeception\Module\UnitHelper::getNonPublicMethod(
            '\Codeception\Module\DrupalUserRegistry',
            "manageTestUsers"
        );
        $this->setExpectedException(
            '\Codeception\Exception\Module',
            "Invalid operation not-an-op when managing users."
        );
        $refMethod->invokeArgs($this->module, array("not-an-op"));
    }

    /**
     * @dataProvider charData
     */
    public function testCtrThrowsExceptionIfBadCharsUsedOnWin($role, $expected)
    {
        if ($expected) {
            $this->setExpectedException('\Codeception\Exception\ModuleConfig');
        }

        $config = array(
            "users" => array(
                "admin" => $role
            ),
        );

        // Mock the class we are testing as we need to mock isWindows method.
        // No other methods are mocked.
        $mock = $this->getMockBuilder('Codeception\Module\DrupalUserRegistry')
            ->setMethods(array("isWindows"))
            ->disableOriginalConstructor()
            ->getMock();

        $mock->method("isWindows")
            ->willReturn(true);

        $mock->__construct($config);
    }

    /**
     * flibble
     *
     * @dataProvider charData
     */
    public function testConstructorAcceptsAnyCharOnLinux($role)
    {
        $config = array(
            "users" => array(
                "admin" => $role,
            ),
        );

        // Mock the class we are testing as we need to mock isWindows method.
        // No other methods are mocked.
        $mock = $this->getMockBuilder('Codeception\Module\DrupalUserRegistry')
            ->setMethods(array("isWindows"))
            ->disableOriginalConstructor()
            ->getMock();

        $mock->method("isWindows")
            ->willReturn(false);

        $mock->__construct($config);
    }

    /**
     * Provide data for testConstructorThrowsExceptionIfBadCharsUsed*
     *
     * @return array
     *  First element is a password, 2nd is role, the 3rd is whether it should
     *  be accepted or not on windows.
     */
    public function charData()
    {
        $fields = array("name", "email", "pass", "roles");
        $chars = array(
            '!' => true,
            '%' => true,
            '"' => true,
            "a" => false
        );

        $params = array();

        foreach ($fields as $field) {
            foreach ($chars as $char => $expected) {
                $params["$char as $field"] = array(
                    array(
                        "name" => "admin",
                        "email" => "admin@example.com",
                        "pass" => "password",
                        "roles" => array("admin"),
                        $field => $field == "roles" ? array($char) : $char,
                    ),
                    $expected
                );
            }
        }

        return $params;
    }
}
