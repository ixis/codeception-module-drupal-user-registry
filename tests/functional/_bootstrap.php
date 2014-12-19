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

Fixtures::add("validModuleConfig", $mockValidModuleConfig);
