<?php

namespace Codeception\Module\Drupal\UserRegistry\Storage;

use Codeception\Module\Drupal\UserRegistry\DrupalTestUser;
use Codeception\Exception\Configuration as ConfigException;
use Codeception\Exception\Module as ModuleException;

/**
 * Class ModuleConfigStorage.
 *
 * @package Codeception\Module\DrupalUserRegistry\Storage
 */
class ModuleConfigStorage implements StorageInterface
{
    /**
     * This regex will be used in preg_replace(), replacing all matches with a full stop. For example, 'forum moderator'
     * becomes 'forum.moderator' and 'high-level administrator' would become 'high.level.administrator'. This is used
     * in conjunction with DRUPAL_USERNAME_PREFIX to create the test users' usernames.
     */
    const DRUPAL_ROLE_TO_USERNAME_PATTERN = '/(\s|-)/';

    /**
     * This string will be used as a prefix for a test user name in conjunction with the replacement pattern above.
     *
     * Using the default value, the examples above will have usernames 'test.forum.moderator' and
     * 'test.high.level.administrator' respectively. This prefix can be overridden in the module's configuration.
     */
    protected $drupalUsernamePrefix = 'test';

    /**
     * @var array
     *   Indexed array of Drupal role machine names.
     */
    protected $roles;

    /**
     * @var string
     *   Password to use for all test users.
     */
    protected $password;

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
        $this->roles = $config['roles'];
        $this->password = $config['password'];

        if (isset($config['username-prefix'])) {
            if (strlen($config['username-prefix']) < 4) {
                throw new ConfigException(sprintf(
                    "Drupal username prefix should contain at least 4 characters (%s).",
                    $config['username-prefix']
                ));
            } else {
                $this->drupalUsernamePrefix = (string)$config['username-prefix'];
            }
        }
    }

    /**
     * Load and return an array of test users.
     *
     * {@inheritdoc}
     */
    public function load()
    {
        return array_map([$this, "mapRoleToTestUser"], array_combine($this->roles, $this->roles));
    }

    /**
     * Generate a test user name from a role name and return the corresponding DrupalTestUser object.
     *
     * @param string $roleName
     *   The role for which to generate a test user.
     *
     * @return \Codeception\Module\Drupal\UserRegistry\DrupalTestUser
     */
    public function mapRoleToTestUser($roleName)
    {
        $roleNameSuffix = preg_replace(self::DRUPAL_ROLE_TO_USERNAME_PATTERN, ".", $roleName);
        $userName = $this->drupalUsernamePrefix . "." . $roleNameSuffix;
        return new DrupalTestUser($userName, $this->password, $roleName);
    }
}
