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
    public function testRootUser(FunctionalTester $I)
    {
        $I->amGoingTo("get root user from loaded configuration");
        $config = \Codeception\Configuration::suiteSettings("acceptance", \Codeception\Configuration::config());

        if (!isset($config["modules"]["config"]["DrupalUserRegistry"]["root"])) {
            \PHPUnit_Framework_Assert::fail("Root user configuration is not set.");
        }
        $moduleRootUserConfig = $config["modules"]["config"]["DrupalUserRegistry"]["root"];

        /** @type DrupalTestUser */
        $rootUser = $I->getRootUser();

        $I->amGoingTo("check the returned data is as expected");
        \PHPUnit_Framework_Assert::assertEquals(
            $moduleRootUserConfig["username"],
            $rootUser->name,
            "Usernames did not match."
        );
        \PHPUnit_Framework_Assert::assertEquals(
            $moduleRootUserConfig["password"],
            $rootUser->pass,
            "Passwords did not match."
        );
        \PHPUnit_Framework_Assert::assertNull($rootUser->roleName, "Role name was not null.");
    }

    /**
     * Test that the result from getLoggedInUser() is what we expect after setting it with setLoggedInUser().
     *
     * @param FunctionalTester $I
     *   Actor object being used to test.
     * @param Scenario $scenario
     *   The running scenario, used to skip this test.
     */
    public function testSetAndGetLoggedInUser(FunctionalTester $I, Scenario $scenario)
    {
        $scenario->skip("functionality is not yet merged");

        $roleToUse = "administrator";

        $I->amGoingTo("set, get then compare the module stored logged in user");
        $user = $I->getUserByRole($roleToUse);
        $I->setLoggedInUser($user);
        $loggedInUser = $I->getLoggedInUser();

        \PHPUnit_Framework_Assert::assertEquals($user, $loggedInUser, "User objects did not match.");
    }
}
