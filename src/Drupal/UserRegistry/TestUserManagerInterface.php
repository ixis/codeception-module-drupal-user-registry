<?php

namespace Codeception\Module\Drupal\UserRegistry;

/**
 * Interface TestUserManagerInterface.
 *
 * @package Codeception\Module\DrupalUserRegistry
 */
interface TestUserManagerInterface
{
    /**
     * Create a test user.
     *
     * @param DrupalTestUser $user
     *   The user to create.
     *
     * @return void
     */
    public function createUser($user);

    /**
     * Create multiple test users.
     *
     * @param array $users
     *   An array of DrupalTestUser objects to create.
     *
     * @return void
     */
    public function createUsers($users);

    /**
     * Delete a test user.
     *
     * @param DrupalTestUser $user
     *   The user to delete.
     *
     * @return void
     */
    public function deleteUser($user);

    /**
     * Delete multiple test users.
     *
     * @param array $users
     *   An array of DrupalTestUser objects to delete.
     *
     * @return void
     */
    public function deleteUsers($users);

    /**
     * Determine if a user with a given username exists.
     *
     * @param string $username
     *   The username to check.
     *
     * @return bool
     *   True if the user exists, false otherwise.
     */
    public function userExists($username);
}
