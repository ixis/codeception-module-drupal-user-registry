<?php

namespace Codeception\Module\Drupal\UserRegistry\Storage;

use Codeception\Module\Drupal\UserRegistry\DrupalTestUser;

/**
 * Interface for retrieving Drupal users from storage.
 *
 * @package Codeception\Module\DrupalUserRegistry\Storage
 */
interface StorageInterface
{
    /**
     * Load and return an array of test users.
     *
     * @return DrupalTestUser[]
     *   Array of DrupalTestUser objects.
     */
    public function load();
}
