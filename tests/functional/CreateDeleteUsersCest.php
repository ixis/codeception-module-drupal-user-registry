<?php

use \FunctionalTester;

use Codeception\Module\Drupal\UserRegistry\Storage\ModuleConfigStorage;
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
     * Always clean up the test users, regardless of configuration, in preparation for subsequent tests.
     *
     * @param FunctionalTester $I
     *   The Actor or StepObject being used to test.
     */
    public function _after(FunctionalTester $I)
    {
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
     * Helper to translate role names to test usernames.
     *
     * @todo This code is copied from ModuleConfigStorage::load(), where it's a bit buried. Needs refactoring.
     *
     * @see ModuleConfigStorage::load()
     *
     * @param string $role
     *   The name of the role to translate into a test username.
     *
     * @return string
     */
    protected function getTestUsername($role)
    {
        $roleNameSuffix = preg_replace(ModuleConfigStorage::DRUPAL_ROLE_TO_USERNAME_PATTERN, ".", $role);
        return ModuleConfigStorage::DRUPAL_USERNAME_PREFIX . "." . $roleNameSuffix;
    }
}
