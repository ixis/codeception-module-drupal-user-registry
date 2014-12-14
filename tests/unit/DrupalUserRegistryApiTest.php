<?php

use \Codeception\Module\Drupal\UserRegistry\DrupalTestUser;
use \Codeception\Module\DrupalUserRegistry;
use \Codeception\Util\Fixtures;

/**
 * Unit tests for the 'public API' methods of the DrupalUserRegistry class.
 *
 * This class only contains tests which cover API methods, i.e. those methods which are composed into the Actor class
 * and available via `$I`. Other, non-API related tests are included in DrupalUserRegistryTest.php
 */
class DrupalUserRegistryApiTest extends \Codeception\TestCase\Test
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
     * Don't use _before() as not all the tests require this setup.
     *
     * Note this function can't be called setUp()...
     */
    protected function initialise()
    {
        $this->module = new \Codeception\Module\DrupalUserRegistry();
        $this->module->_setConfig(Fixtures::get("validModuleConfig"));
        $this->module->_initialize();
    }

    /**
     * Test getRootUser()
     *
     * @test
     * @group api
     *
     * @throws \Codeception\Exception\Module
     */
    public function testGetRootUser()
    {
        $this->initialise();
        $config = Fixtures::get("validModuleConfig");
        $rootUser = $this->module->getRootUser();

        $this->tester->amGoingTo("check the returned data is as expected");
        // @todo Convert $config to DrupalTestUser, in order to use assertTestUsersEqual() helper.
        $this->assertEquals($config["root"]["username"], $rootUser->name, "Usernames did not match.");
        $this->assertEquals($config["root"]["password"], $rootUser->pass, "Passwords did not match.");

        // Role for root user is always null.
        $this->assertNull($rootUser->roleName, "Role name was not null.");
    }

    /**
     * Test the expected exceptions are thrown when the module is not configured enough to uset getRootUser()
     *
     * @test
     * @group api
     */
    public function testGetRootUserThrowsExceptionWhenUsernameNotConfigured()
    {
        $this->getRootUserMisconfigure(["username"]);
        $this->getRootUserMisconfigure(["password"]);
        $this->getRootUserMisconfigure(["username", "password"]);
    }

    /**
     * Helper for testGetRootUserThrowsExceptionWhenUsernameNotConfigured()
     *
     * @param array $keysToUnset
     *   List of keys to unset from $config["root"] array.
     *
     * @throws \Codeception\Exception\Module
     */
    protected function getRootUserMisconfigure($keysToUnset)
    {
        // Grab a valid module configuration but remove the root user's username.
        $config = Fixtures::get("validModuleConfig");

        foreach ($keysToUnset as $keyToUnset) {
            unset($config["root"][$keyToUnset]);
        }

        $this->module = new \Codeception\Module\DrupalUserRegistry();
        $this->module->_setConfig($config);
        $this->module->_initialize();

        $this->setExpectedException(
            '\Codeception\Exception\Module',
            "Credentials for the root user (username, password) are not configured."
        );
        $this->module->getRootUser();
    }

    /**
     * Test getUser()
     *
     * @test
     * @group api
     */
    public function testGetUser()
    {
        $this->initialise();

        $expected = new DrupalTestUser("test.administrator", "test123!", "administrator");
        $this->assertTestUsersEqual($expected, $this->module->getUser("test.administrator"));

        $this->assertFalse(
            $this->module->getUser("invalid.test.user"),
            "Result from getUser() was not false when requesting an invalid test user."
        );
    }

    /**
     * Test getUserByRole()
     *
     * @test
     * @group api
     */
    public function testGetUserByRole()
    {
        $this->initialise();
        $expected = new DrupalTestUser("test.administrator", "test123!", "administrator");
        $this->assertTestUsersEqual($expected, $this->module->getUserByRole("administrator"));
    }

    /**
     * Test getRoles()
     *
     * @test
     * @group api
     */
    public function testGetRoles()
    {
        $this->initialise();
        $expected = ["administrator", "editor", "moderator", "Authenticated"];
        $this->assertEquals($expected, $this->module->getRoles());

    }

    /**
     * Expect to see getLoggedInUser return null before a logged in user is set.
     *
     * @test
     * @group api
     */
    public function testGetLoggedInUserIsNullBeforeAnyUserIsSet()
    {
        $this->initialise();
        $loggedInUser = $this->module->getLoggedInUser();
        $this->assertNull(
            $loggedInUser,
            "getLoggedInUser() returned something other than null before setLoggedInUser() was called."
        );
    }

    /**
     * Sequential test for the three 'logged in user' helper methods.
     *
     * Test that the result from getLoggedInUser() is what we expect after setting it with setLoggedInUser(), then
     * returns null after calling removeLoggedInUser()
     *
     * @test
     * @group api
     */
    public function testSetGetRemoveLoggedInUserHelpers()
    {
        $this->initialise();

        // Call set with expected test values.
        $testUser = Fixtures::get("drupalTestUser");
        $this->module->setLoggedInUser($testUser);
        // @todo verify set (WITHOUT calling get?)

        $loggedInUser = $this->module->getLoggedInUser();

        // Check the returned data is as expected.
        $this->assertTestUsersEqual($testUser, $loggedInUser);

        // Remove logged in user and ensure is now null.
        $this->module->removeLoggedInUser();
        $this->assertNull($this->module->getLoggedInUser());
    }

    /**
     * Test that the returned result from getLoggedInUser() is null after calling removeLoggedInUser()
     *
     * Note that this member variable won't be initialised during this test, so it will be null regardless. How to
     * verify this method works in isolation? This test at least verifies that removeLoggedInUser() doesn't actually
     * set the logged in user to anything other than null.
     *
     * @test
     * @group api
     */
    public function testRemoveLoggedInUser()
    {
        $this->initialise();
        $this->module->removeLoggedInUser();
        $this->assertNull($this->module->getLoggedInUser(), "Value returned form getLoggedInUser() was not null.");
    }

    /**
     * Helper when asserting DrupalTestUser objects are equal.
     *
     * @todo This is here because of issues using assertEquals() on objects. Needs looking into.
     *
     * @param DrupalTestUser $expected
     *   The expected test user.
     * @param mixed $actual
     *   The actual "test user" returned during the test.
     * @param bool $checkRole
     *   When true, the test users' roles will also be compared.
     */
    protected function assertTestUsersEqual(DrupalTestUser $expected, $actual, $checkRole = false)
    {
        $this->assertEquals($expected->name, $actual->name, "Usernames did not match.");
        $this->assertEquals($expected->pass, $actual->pass, "Passwords did not match.");

        if ($checkRole) {
            $this->assertEquals($expected->roleName, $actual->roleName, "Role names did not match.");
        }
    }
}
