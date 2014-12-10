<?php
/**
 * Here you can initialize variables that will be available to your tests.
 */

use \Codeception\Util\Fixtures;

// Define a complete, valid configuration identical to that configured in the functional suite.
$mockValidModuleConfig = array(
    "roles" => array("administrator", 'editor', 'moderator', 'Authenticated'),
    "password" => "test123!",
    "create" => false,
    "delete" => false,
    "drush-alias" => "@d7.local",
    "root" => array(
        "username" => "root",
        "password" => "root",
    ),
);

// Define a configuration which is invalid solely because the drush-alias entry is missing.
$mockInvalidModuleConfig = array(
    "roles" => array("administrator", 'editor', 'moderator', 'Authenticated'),
    "password" => "test123!",
    "create" => false,
    "delete" => false,
    "root" => array(
        "username" => "root",
        "password" => "root",
    ),
);

Fixtures::add("validModuleConfig", $mockValidModuleConfig);
Fixtures::add("invalidModuleConfig", $mockInvalidModuleConfig);

// Define a mock test user.
$drupalTestUser = new stdClass();
$drupalTestUser->name = "test.mock.user";
$drupalTestUser->pass = "password";
$drupalTestUser->roleName = "mock";
Fixtures::add("drupalTestUser", $drupalTestUser);
