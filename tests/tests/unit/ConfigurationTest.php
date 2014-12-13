<?php

use \Codeception\Module\Drupal\UserRegistry\DrupalTestUser;
use \Codeception\Util\Fixtures;

/**
 * Unit tests for DrushTestUserManager class.
 */
class ConfigurationTest extends \Codeception\TestCase\Test
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
        $this->module = new \Codeception\Module\DrupalUserRegistry();
        $this->module->_setConfig(Fixtures::get("validModuleConfig"));
        $this->module->_initialize();
    }

    /**
     * Test the root user is returned as configured.
     *
     * @test
     * @group api
     */
    public function testGetRootUserFromConfiguration()
    {
        $this->tester->amGoingTo("get root user from loaded configuration");
        // @todo We are loading config from a suite with hard-coded name here. Sort this out.
        $config = \Codeception\Configuration::suiteSettings("acceptance", \Codeception\Configuration::config());

        if (!isset($config["modules"]["config"]["DrupalUserRegistry"]["root"])) {
            \PHPUnit_Framework_Assert::fail("Root user configuration is not set.");
        }
        $moduleRootUserConfig = $config["modules"]["config"]["DrupalUserRegistry"]["root"];

        /** @type DrupalTestUser */
        $rootUser = $this->module->getRootUser();

        // Check the returned data is as expected.
        // @todo These 3 lines could be replaced with a call to assertTestUsersEqual() if it was refactored out of
        // DrupalUserRegistyTest.php
        $this->assertEquals($moduleRootUserConfig["username"], $rootUser->name, "Usernames did not match.");
        $this->assertEquals($moduleRootUserConfig["password"], $rootUser->pass, "Passwords did not match.");
        $this->assertNull($rootUser->roleName, "Role name was not null.");
    }
}
