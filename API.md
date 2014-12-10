Drupal User Registry public API
===

The following examples define the public API for the Drupal User Registry Codeception module. All the methods listed here will be available to the Actor object `$I` when using this module.

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

// Returns a DrupalTestUser object representing the "root" user (account with uid 1), if credentials are configured:
$rootUser = $I->getRootUser();

// Sets the user who is currently logged in. One of three utility functions allowing tests to establish which user is currently logged in.
$person = $I->getUserByRole("administrator");
$I->setLoggedInUser($person);

// Gets the currently logged in user. One of three utility functions allowing tests to establish which user is currently logged in.
$loggedInUser = $I->getLoggedInUser();

// Removes the currently logged in user. One of three utility functions allowing tests to establish which user is currently logged in.
$I->removeLoggedInUser();
```
