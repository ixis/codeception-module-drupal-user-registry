<?php
/**
 * Here you can initialize variables that will be available to your tests.
 */

use Codeception\Util\Fixtures;

// Define a complete, valid configuration identical to that configured in the functional suite.
$mockValidModuleConfig = array(
    "roles" => array("administrator", 'editor', 'moderator', 'Authenticated'),
    "password" => "test123!",
    "create" => true,
    "delete" => true,
    "drush-alias" => "@d7.local",
    "root" => array(
        "username" => "root",
        "password" => "root",
    ),
);

// Define a complete, valid configuration with email addresses for some roles.
//
// NOTE: don't use admin@example.com for the administrator test user, as the
// site we're testing against has uid 1 pre-configured with this address, and
// the test user is not created.
$mockValidModuleConfigWithEmails = array_merge(
    array("emails" => array("administrator" => "administrator@example.com", "editor" => "editor@example.com")),
    $mockValidModuleConfig
);

Fixtures::add("validModuleConfig", $mockValidModuleConfig);
Fixtures::add("validModuleConfigWithEmails", $mockValidModuleConfigWithEmails);
