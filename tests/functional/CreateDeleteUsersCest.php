<?php

use \FunctionalTester;

use Codeception\Util\Fixtures;

/**
 * Test the creation and deletion of users when a "suite" is run.
 */
class CreateDeleteUsersCest
{
    /**
     * @var \Codeception\Module\DrupalUserRegistry
     *   Instance of the module being used in tests.
     */
    protected $module;

    /**
     * @var array
     *   Module configuration used to initialize $this->module
     */
    protected $moduleConfig;

    /**
     * @param FunctionalTester $I
     *   The Actor or StepObject being used to test.
     */
    public function _before(FunctionalTester $I)
    {
        $this->module = new \Codeception\Module\DrupalUserRegistry();
        $this->moduleConfig = Fixtures::get("validModuleConfig");
        $this->module->_setConfig($this->moduleConfig);
        $this->module->_initialize();
    }

    /**
     * Clean up the test users (regardless of current module configuration) in preparation for subsequent tests.
     *
     * Note this method of cleaning up the test users will only be used when Db cleanup is set to false.
     *
     * @param FunctionalTester $I
     *   The Actor or StepObject being used to test.
     */
    public function _after(FunctionalTester $I)
    {
        // Only need to use the module to clean up if we're not doing it with Db module.
        $config = Codeception\Configuration::config();
        if (isset($config["modules"]["config"]["Db"]["cleanup"])
            && $config["modules"]["config"]["Db"]["cleanup"] == true) {
            return;
        }

        $config = $this->moduleConfig;
        $config["delete"] = true;
        $this->module->_reconfigure($config);
        $this->module->_afterSuite();
    }

    /**
     * Test that users are created when create = true and delete = true.
     *
     * @param FunctionalTester $I
     *   The Actor or StepObject being used to test.
     */
    public function testUsersAreCreated(FunctionalTester $I)
    {
        $this->module->_beforeSuite();
        foreach ($this->moduleConfig["roles"] as $role) {
            $I->seeInDatabase("users", array("name" => $this->getTestUsername($role)));
        }
    }

    /**
     * Test users are deleted when create = true and delete = true.
     *
     * @param FunctionalTester $I
     *   The Actor or StepObject being used to test.
     */
    public function testUsersAreDeleted(FunctionalTester $I)
    {
        $this->module->_beforeSuite();
        $this->module->_afterSuite();
        foreach ($this->moduleConfig["roles"] as $role) {
            $I->dontSeeInDatabase("users", array("name" => $this->getTestUsername($role)));
        }
    }

    /**
     * Test users are created but not deleted when create = true and delete = false.
     *
     * @param FunctionalTester $I
     *   The Actor or StepObject being used to test.
     */
    public function testUsersAreCreatedButNotDeleted(FunctionalTester $I)
    {
        $config = $this->moduleConfig;
        $config["delete"] = false;
        $this->module->_reconfigure($config);

        $this->module->_beforeSuite();
        foreach ($this->moduleConfig["roles"] as $role) {
            $I->seeInDatabase("users", array("name" => $this->getTestUsername($role)));
        }

        $this->module->_afterSuite();
        foreach ($this->moduleConfig["roles"] as $role) {
            $I->seeInDatabase("users", array("name" => $this->getTestUsername($role)));
        }
    }

    /**
     * Test users are created with the correct roles.
     *
     * @param FunctionalTester $I
     *   The Actor or StepObject being used to test.
     */
    public function testCreatedUsersHaveCorrectRoles(FunctionalTester $I)
    {
        $this->module->_beforeSuite();

        // Grab a map of role name => test user $uid from the database. This assumes the current 1-1 relationship
        // between roles and test users.
        $users = array();
        foreach ($this->moduleConfig["roles"] as $role) {
            if ($role != "Authenticated") {
                $uid = $I->grabFromDatabase("users", "uid", array("name" => $this->getTestUsername($role)));
                $users[$role] = $uid;
            }
        }

        foreach ($this->moduleConfig["roles"] as $role) {
            if ($role != "Authenticated") {
                $rid = $I->grabFromDatabase("role", "rid", array("name" => $role));
                $I->seeInDatabase("users_roles", array("uid" => $users[$role], "rid" => $rid));
            }
        }
    }
}
