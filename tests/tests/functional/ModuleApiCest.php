<?php

use \FunctionalTester;

use \Codeception\Module\Drupal\UserRegistry\DrupalTestUser;

/**
 * Tests public methods made available to $I as part of the module.
 *
 * @group api
 */
class ModuleApiCest
{
    /**
     * Test the root user is returned as configured.
     *
     * @param FunctionalTester $I
     *   Actor object being used to test.
     */
    public function testGetRootUser(FunctionalTester $I)
    {
        // Describe this test, overriding the name Codeception generates from the method name.
        $I->wantTo("test getLoggedInUser()");

        $I->amGoingTo("get root user from loaded configuration");
        $config = \Codeception\Configuration::suiteSettings("acceptance", \Codeception\Configuration::config());

        if (!isset($config["modules"]["config"]["DrupalUserRegistry"]["root"])) {
            \PHPUnit_Framework_Assert::fail("Root user configuration is not set.");
        }
        $moduleRootUserConfig = $config["modules"]["config"]["DrupalUserRegistry"]["root"];

        /** @type DrupalTestUser */
        $rootUser = $I->getRootUser();

        $I->amGoingTo("check the returned data is as expected");
        $I->assertEquals($moduleRootUserConfig["username"], $rootUser->name, "Usernames did not match.");
        $I->assertEquals($moduleRootUserConfig["password"], $rootUser->pass, "Passwords did not match.");
        $I->assertNull($rootUser->roleName, "Role name was not null.");
    }

    /**
     * Test that the result from getLoggedInUser() is what we expect after setting it with setLoggedInUser().
     *
     * @param FunctionalTester $I
     *   Actor object being used to test.
     */
    public function testSetAndGetLoggedInUser(FunctionalTester $I)
    {
        $roleToUse = "administrator";

        $I->amGoingTo("set, get then compare the logged in user stored by the module against that originally set");
        $user = $I->getUserByRole($roleToUse);
        $I->setLoggedInUser($user);
        $loggedInUser = $I->getLoggedInUser();
        $I->assertEquals($user, $loggedInUser, "User objects did not match.");
    }

    /**
     * Test that the returned result from getLoggedInUser() is null after calling removeLoggedInUser().
     *
     * @param FunctionalTester $I
     *   Actor object being used to test.
     */
    public function testRemoveLoggedInUser(FunctionalTester $I)
    {
        // Describe this test, overriding the name Codeception generates from the method name.
        $I->wantTo("test removeLoggedInUser()");

        $I->removeLoggedInUser();
        $I->expect("value returned from getLoggedInUser to be null");
        $I->assertNull($I->getLoggedInUser(), "Value returned form getLoggedInUser() was not null.");
    }
}
