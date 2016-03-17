<?php

namespace Codeception\Module\Drupal\UserRegistry\Storage;

use Codeception\Module\Drupal\UserRegistry\DrupalTestUser;
use Codeception\Exception\Configuration as ConfigException;
use BadMethodCallException;

/**
 * Class ModuleConfigStorage.
 *
 * @package Codeception\Module\DrupalUserRegistry\Storage
 */
class ModuleConfigStorage implements StorageInterface
{
    /**
     * Array of DrupalTestUser objects.
     *
     * @var DrupalTestUser[]
     */
    protected $users = array();

    /**
     * Whether the $users property has been loaded.
     *
     * @var bool
     */
    protected $loaded = false;

    /**
     * A yaml-loaded array as loaded from the Codeception yaml config.
     *
     * @var array
     */
    protected $yaml;

    /**
     * Check for required module configuration and initialize.
     *
     * @param array $config
     *   Array containing the DrupalUserRegistry module configuration.
     *
     * @throws \Codeception\Exception\Configuration
     */
    public function __construct($config)
    {
        if (!isset($config['users'])) {
            throw new BadMethodCallException('No "users" property found in yaml configuration.');
        } else {
            $this->yaml = $config;
            $this->load();
        }
    }

    /**
     * Load and return an array of test users.
     *
     * {@inheritdoc}
     */
    public function load()
    {
        // Don't load the users from yaml if we have already loaded them.
        if ($this->loaded) {
            return $this->users;
        }

        $root_is_defined = false;

        // Ensure we have yaml to load users from.
        if (empty($this->yaml) || empty($this->yaml['users'])) {
            throw new BadMethodCallException('No yaml has been defined in load() method. Cannot load users.');
        }

        // Set up a default password if one was provided.
        $defaultPass = isset($this->yaml['defaultPass']) ? $this->yaml['defaultPass'] : '';

        // Load each user from the yaml.
        foreach ($this->yaml['users'] as $item) {
            $user = new DrupalTestUser(
                $item['name'],
                empty($item['pass']) ? $defaultPass : $item['pass'],
                $item['roles'],
                $item['email']
            );

            // If user is marked as root user, save this to the user object.
            if (isset($item['root']) && $item['root'] == true) {
                if ($root_is_defined) {
                    throw new ConfigException("The 'root' user can only be defined once");
                }
                $user->isRoot = true;
                $root_is_defined = true;
            }

            // Save the user to the collection.
            $this->users[$item['name']] = $user;
        }

        $this->loaded = true;
        return $this->users;
    }
}
