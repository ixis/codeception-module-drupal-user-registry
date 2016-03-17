<?php
/**
 * Here you can initialize variables that will be available to your tests.
 */

use Codeception\Util\Fixtures;

// Define a complete, valid configuration identical to that configured in the functional suite.
$mockValidModuleConfig = array(
    "create" => true,
    "delete" => true,
    "users" => array(
        "administrator" => array(
            "name" => "test.administrator",
            "email" => "test.administrator@example.com",
            "pass" => "foo",
            "roles" => array("administrator", "editor"),
        ),
        "editor" => array(
            "name" => "test.editor",
            "email" => "test.editor@example.com",
            "pass" => "foo",
            "roles" => array("editor", "moderator"),
        ),
        "moderator" => array(
            "name" => "test.moderator",
            "email" => "test.moderator@example.com",
            "pass" => "foo",
            "roles" => array("moderator"),
        ),
    ),
    "drush-alias" => "@d7.local",
);
Fixtures::add("validModuleConfig", $mockValidModuleConfig);

$mockValidModuleConfigWithRoot = array(
    "create" => true,
    "delete" => true,
    "users" => array(
        "root" => array(
            "name" => "root",
            "email" => "test.root@example.com",
            "pass" => "foo",
            "roles" => array(),
            "root" => true,
        ),
    ),
    "drush-alias" => "@d7.local",
);
Fixtures::add("validModuleConfigWithRoot", $mockValidModuleConfigWithRoot);
