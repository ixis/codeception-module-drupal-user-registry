<?php

namespace Codeception\Module\Drupal\UserRegistry;

use Codeception\Module\Drupal\UserRegistry\Storage\StorageInterface;

/**
 * Interface TestUserManagerInterface.
 *
 * @package Codeception\Module\DrupalUserRegistry
 */
interface TestUserManagerInterface
{
    /**
     * Gets the storage object.
     *
     * @return StorageInterface
     *   The Storage object currently set.
     */
    public function getStorage();

    /**
     * Set the storage object.
     *
     * @param StorageInterface $storage
     *   The Storage object to set.
     */
    public function setStorage(StorageInterface $storage);

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
