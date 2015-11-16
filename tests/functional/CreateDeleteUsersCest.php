<?php

use \FunctionalTester;

use Codeception\Module\Drupal\UserRegistry\Storage\ModuleConfigStorage;
use Codeception\Module\DrupalUserRegistry;
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
     * @var FunctionalTester
     *   Store the current Tester object.
     */
    protected $tester;

    /**
     * @param FunctionalTester $I
     *   The Actor or StepObject being used to test.
     */
    public function _before(FunctionalTester $I)
    {
        $this->module = new \Codeception\Module\DrupalUserRegistry();
        $this->moduleConfig = Fixtures::get("validModuleConfig");
        $this->module->_setConfig($this->moduleConfig);
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
        $this->module->_initialize();
        $this->module->_beforeSuite();
        foreach ($this->moduleConfig["users"] as $user) {
            $I->seeInDatabase("users", array("name" => $user["name"]));
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
        $this->module->_initialize();
        $this->module->_beforeSuite();
        $this->module->_afterSuite();
        foreach ($this->moduleConfig["users"] as $user) {
            $I->dontSeeInDatabase("users", array("name" => $user["name"]));
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

        $this->module->_initialize();
        $this->module->_beforeSuite();
        foreach ($this->moduleConfig["users"] as $user) {
            $I->seeInDatabase("users", array("name" => $user["name"]));
        }

        $this->module->_afterSuite();
        foreach ($this->moduleConfig["users"] as $user) {
            $I->seeInDatabase("users", array("name" => $user["name"]));
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
        $this->module->_initialize();
        $this->module->_beforeSuite();

        // Grab a mapping of username => test user $uid from the database.
        $this->tester = $I;
        $createdUsers = $this->usernameToTestUserUidMap();

        foreach ($this->moduleConfig["users"] as $user) {
            foreach ($user["roles"] as $role) {
                if ($role != "Authenticated") {
                    $rid = $I->grabFromDatabase("role", "rid", array("name" => $role));
                    $I->seeInDatabase("users_roles", array("uid" => $createdUsers[$user["name"]], "rid" => $rid));
                }
            }
        }
    }

    /**
     * Test users are created with the correct email addresses.
     *
     * @param FunctionalTester $I
     *   The Actor or StepObject being used to test.
     */
    public function testCreatedUsersHaveCorrectEmails(FunctionalTester $I)
    {
        $this->module->_initialize();
        $this->module->_beforeSuite();

        // Grab a mapping of role name => test user $uid from the database.
        $this->tester = $I;
        $createdUsers = $this -> usernameToTestUserUidMap();

        foreach ($this->moduleConfig["users"] as $user) {
            $I->seeInDatabase("users", array(
                "uid" => $createdUsers[$user["name"]],
                "mail" => $user["email"],
            ));
        }
    }

    /**
     * Return a mapping of username => test user $uid from the database.
     */
    protected function usernameToTestUserUidMap()
    {
        $I = $this->tester;

        $users = array();
        foreach ($this->moduleConfig["users"] as $user) {
            $uid = $I->grabFromDatabase("users", "uid", array("name" => $user["name"]));
            $users[$user["name"]] = $uid;
        }
        return $users;
    }
}
