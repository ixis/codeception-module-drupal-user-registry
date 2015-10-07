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
        foreach ($this->moduleConfig["roles"] as $role) {
            $I->seeInDatabase("users", array("name" => $this->getTestUsername($role)));
        }
    }

    /**
     * Test that users are created with the correct names a configurable prefix is used.
     *
     * @param FunctionalTester $I
     *   The Actor or StepObject being used to test.
     */
    public function testUsersAreCreatedWithCustomPrefix(FunctionalTester $I)
    {
        $config = $this->moduleConfig;
        $prefix = uniqid();
        $config["username-prefix"] = $prefix;
        $this->module->_reconfigure($config);

        $this->module->_initialize();
        $this->module->_beforeSuite();

        foreach ($this->moduleConfig["roles"] as $role) {
            $I->seeInDatabase("users", array("name" => $this->getTestUsername($role, $prefix)));
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

        $this->module->_initialize();
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
        $this->module->_initialize();
        $this->module->_beforeSuite();

        // Grab a mapping of role name => test user $uid from the database.
        $this->tester = $I;
        $users = $this->roleNameToTestUserUidMap();

        foreach ($this->moduleConfig["roles"] as $role) {
            if ($role != "Authenticated") {
                $rid = $I->grabFromDatabase("role", "rid", array("name" => $role));
                $I->seeInDatabase("users_roles", array("uid" => $users[$role], "rid" => $rid));
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
        // Don't use $this->module here, as we're using a different configuration.
        $module = new \Codeception\Module\DrupalUserRegistry();
        $configWithEmails = Fixtures::get("validModuleConfigWithEmails");
        $module->_setConfig($configWithEmails);
        $module->_initialize();

        $module->_beforeSuite();

        // Grab a mapping of role name => test user $uid from the database.
        $this->tester = $I;
        $users = $this->roleNameToTestUserUidMap();

        foreach ($this->moduleConfig["roles"] as $role) {
            if ($role != "Authenticated") {
                // Determine whether we should look for a user configured email
                // address, or the default one.
                if (!array_key_exists($role, $configWithEmails["emails"])) {
                    $email = $this->getTestUsername($role) . '@' . DrupalUserRegistry::DRUPAL_USER_EMAIL_DOMAIN;
                } else {
                    $email = $configWithEmails["emails"]["$role"];
                }
                $I->seeInDatabase("users", array(
                    "uid" => $users[$role],
                    "mail" => $email,
                ));
            }
        }
    }

    /**
     * Return a mapping of role name => test user $uid from the database.
     *
     * This assumes the current 1-1 relationship between roles and test users.
     */
    protected function roleNameToTestUserUidMap()
    {
        $I = $this->tester;

        $users = array();
        foreach ($this->moduleConfig["roles"] as $role) {
            if ($role != "Authenticated") {
                $uid = $I->grabFromDatabase("users", "uid", array("name" => $this->getTestUsername($role)));
                $users[$role] = $uid;
            }
        }
        return $users;
    }

    /**
     * Helper to translate role names to test usernames.
     *
     * @todo This code relies on using ModuleConfigStorage::mapRoleToTestUser()
     *
     * @see ModuleConfigStorage::mapRoleToTestUser()
     *
     * @param string $role
     *   The name of the role to translate into a test username.
     * @param null $prefix
     *
     *
     * @return string
     */
    protected function getTestUsername($role, $prefix = null)
    {
        $config = $this->moduleConfig;
        if ($prefix) {
            $config["username-prefix"] = $prefix;
        }
        $dummyStorage = new ModuleConfigStorage($config);
        $testUser = $dummyStorage->mapRoleToTestUser($role);
        return $testUser->name;
    }
}
