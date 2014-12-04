Drupal User Registry - a Codeception module for managing test users
===

A Codeception module for managing test users on Drupal sites.

## Install with Composer

This module is available on [Packagist](https://packagist.org/packages/pfaocle/codeception-module-drupal-user-registry) and can be installed with Composer:

    {
        "require": {
            "codeception/codeception": "2.0.*",
            "pfaocle/codeception-module-drupal-user-registry": "dev-master"
        }
    }

Drupal User Registry minimally requires Codeception 2.0 and PHP 5.4

## Example suite configuration

    class_name: AcceptanceTester
    modules:
        enabled:
            - PhpBrowser
            - AcceptanceHelper
            - DrupalUserRegistry
        config:
            PhpBrowser:
                url: 'http://localhost/myapp/'
            DrupalUserRegistry:
                roles: ['administrator', 'editor', 'sub editor', 'lowly-user', 'authenticated']  # A list of user roles.
                password: 'test123!'         # The password to use for all test users.
                create: true                 # Whether to create all defined test users at the start of the suite.
                delete: true                 # Whether to delete all defined test users at the end of the suite.
                drush-alias: '@mysite.local' # The Drush alias to use when managing users via DrushTestUserManager.


## Creating or deleting users using the command line

Once this module is installed in a Codeception test suite the following commands can be used to create and delete test users (from the root of the test suite):

    # Create test users for all defined roles.
    vendor/bin/drupal-user-registry users:create

    # Delete test users for all defined roles.
    vendor/bin/drupal-user-registry users:delete


## Acknowledgements

Props to [Andy Rigby](https://github.com/ixisandyr) for the storage code and inspiration.
