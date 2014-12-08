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
}
