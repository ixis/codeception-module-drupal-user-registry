<?php

namespace Codeception\Module\Drupal\UserRegistry;

/**
 * Class DrupalTestUser - represents a user on the Drupal site being tested.
 *
 * @package Codeception\Module
 */
class DrupalTestUser
{
    /**
     * @var string
     *   The username of this person.
     */
    public $name;

    /**
     * @var string
     *   The password for this user's account.
     */
    public $pass;

    /**
     * @var null|string
     *   The role that this user should be given.
     */
    public $roleName;

    /**
     * Constructor.
     *
     * @param string $name
     *   The username of this person.
     * @param string $pass
     *   The password for this user's account.
     * @param string|null $roleName
     *   The role that this user should be given.
     */
    public function __construct($name, $pass, $roleName = null)
    {
        $this->name = $name;
        $this->pass = $pass;
        $this->roleName = $roleName;
    }

    /**
     * Implement __toString().
     *
     * Codeception will now print the name rather than class name.
     *
     * @return string
     *   The name.
     */
    public function __toString()
    {
        return $this->name;
    }
}
