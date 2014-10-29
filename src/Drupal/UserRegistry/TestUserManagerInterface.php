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
     */
    public function createUser($user);

    /**
     * Create multiple test users.
     *
     * @param array $users
     *   An array of DrupalTestUser objects to create.
     */
    public function createUsers($users);

    /**
     * Delete a test user.
     *
     * @param DrupalTestUser $user
     *   The user to delete.
     */
    public function deleteUser($user);

    /**
     * Delete multiple test users.
     *
     * @param array $users
     *   An array of DrupalTestUser objects to delete.
     */
    public function deleteUsers($users);
}
