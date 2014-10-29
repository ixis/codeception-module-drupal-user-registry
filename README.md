Drupal User Registry
===

## A Codeception module for managing test users

_Drupal User Registry_ is a [Codeception module](http://codeception.com/addons) for managing test users on [Drupal](https://www.drupal.org/) sites. It can be configured to automatically create users before and delete users after a suite run.

It also allows the use of the following statements in tests:

```php
// Returns a DrupalTestUser object representing the test user available for this role.
$user = $I->getUserByRole($roleName);

// Returns a DrupalTestUser object representing the user, or false if no users were found. Note this will only
// return a user defined and managed by this module, it will not return information about arbitrary accounts
// on the site being tested.
$user = $I->getUser($userName);

// Returns an indexed array of configured roles, for example:
//   array(
//     0 => 'administrator',
//     1 => 'editor',
//     2 => ...
//   );
$roles = $I->getRoles();
```

The **DrupalTestUser** class is a very minimal representation of a Drupal user account and can be used as part of a login procedure defined in, for example, a StepObject or PageObject.


## Install with Composer

This module is available on [Packagist](https://packagist.org/packages/pfaocle/codeception-module-drupal-user-registry) and can be installed with Composer:

```json
{
    "require": {
        "codeception/codeception": "2.0.*",
        "pfaocle/codeception-module-drupal-user-registry": "dev-master"
    }
}
```

Drupal User Registry minimally requires Codeception 2.0 and PHP 5.4


## Example suite configuration

```yaml
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
```

**Note** that only a list of user roles is defined - no specific usernames. This is because we only need a single representative user account for a given role performing an acceptance test. Each role defined in configuration maps directly to a single user with username derived from the role name. For example, the configuration above would result in the following usernames: _test.administrator_, _test.editor_, _test.sub.editor_, _test.lowly.user_, _test.authenticated_.

The derivative usernames are always prefixed with _test._ and have any character in the role name matching the regex `/(\s|-)/` (i.e. whitespace and hyphens) replaced with a full-stop character (`.`).


## Acknowledgements

Props to [Andy Rigby](https://github.com/ixisandyr) for the storage code and inspiration.
