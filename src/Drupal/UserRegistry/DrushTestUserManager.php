<?php

namespace Codeception\Module\Drupal\UserRegistry;

use Codeception\Exception\Configuration as ConfigurationException;
use Codeception\Exception\Module as ModuleException;
use Codeception\Lib\Console\Message;
use Codeception\Lib\Console\Output;
use Codeception\Module\DrupalUserRegistry;
use Codeception\Util\Debug;

/**
 * Class DrushTestUserManager: create and delete test users via Drush and Drush aliases.
 *
 * @package Codeception\Module\DrupalUserRegistry
 */
class DrushTestUserManager implements TestUserManagerInterface
{
    /**
     * @var string
     *   The Drush alias on which to run user management commands. Note this is stored WITH the leading @ character.
     */
    protected $alias;

    /**
     * Constructor: ensure we have all the configuration values we need and store them.
     *
     * @param array $config
     *   The DrupalUserRegistry module configuration.
     *
     * @throws \Codeception\Exception\Configuration
     */
    public function __construct($config)
    {
        if (!isset($config['drush-alias'])) {
            throw new ConfigurationException("Please configure the drush-alias setting in your suite configuration.");
        }
        $this->alias = $config['drush-alias'];
        $this->output = new Output(array());
    }

    /**
     * Create a test user via Drush and exec().
     *
     * {@inheritdoc}
     */
    public function createUser($user)
    {
        Debug::debug("Trying to create test user '{$user->name}' on '{$this->alias}'.");

        if ($this->userExists($user->name)) {
            $this->message(
                "User '{$user->name}' already exists on {$this->alias}, skipping.",
                new Output(array())
            )->writeln();
        } else {
            // Create the user.
            $this->message("Creating test user '{$user->name}' on {$this->alias}.", new Output(array()))->writeln();
            $this->runDrush(
                sprintf(
                    "user-create %s --mail=%s --password=%s",
                    escapeshellarg($user->name),
                    escapeshellarg($user->name . "@" . DrupalUserRegistry::DRUPAL_USER_EMAIL_DOMAIN),
                    escapeshellarg($user->pass)
                )
            );

            // Add a role, if set.
            if ($user->roleName != "Authenticated") {
                $this->runDrush(
                    sprintf(
                        "user-add-role %s --name=%s",
                        escapeshellarg($user->roleName),
                        escapeshellarg($user->name)
                    )
                );
            }
        }
    }

    /**
     * Create multiple test users.
     *
     * {@inheritdoc}
     */
    public function createUsers($users)
    {
        foreach ($users as $user) {
            $this->createUser($user);
        }
    }

    /**
     * Delete a test user via Drush and exec().
     *
     * {@inheritdoc}
     */
    public function deleteUser($user)
    {
        $this->message("Deleting test user {$user->name} on {$this->alias}.")->writeln();
        $this->runDrush(
            sprintf(
                "user-cancel %s --delete-content",
                $user->name
            )
        );
    }

    /**
     * Delete multiple test users.
     *
     * {@inheritdoc}
     */
    public function deleteUsers($users)
    {
        foreach ($users as $user) {
            $this->deleteUser($user);
        }
    }

    /**
     * Determine if a user with a given username exists.
     *
     * {@inheritdoc}
     *
     * @throws ModuleException
     */
    public function userExists($username)
    {
        $jsonOutput = $this->runDrush("user-information " . escapeshellarg($username) . " --format=json");

        if (!is_array($jsonOutput)) {
            throw new ModuleException(__CLASS__, "Response from Drush was not an array as expected.");
        }

        $jsonResult = array_pop($jsonOutput);
        $jsonUser = json_decode($jsonResult, true);

        if (!is_null($jsonUser)) {
            $jsonUser = array_pop($jsonUser);
            if (isset($jsonUser["name"]) && $jsonUser["name"] == $username) {
                // This test user already exists.
                return true;
            } else {
                throw new ModuleException(__CLASS__, "Drush returned a user but the username did not match.");
            }
        }

        return false;
    }

    /**
     * Run a Drush command.
     *
     * @param string $cmd
     *   The Drush command, without executable and alias, e.g. "pml". The arguments should be escaped.
     *
     * @return array
     *   Array of lines output from the Drush command.
     */
    protected function runDrush($cmd)
    {
        $cmdOutput = array();
        exec($this->prepareDrushCommand($cmd), $cmdOutput);

        return array_filter(
            $cmdOutput,
            function ($line) {
                return strpos($line, "Warning: Permanently added") !== 0;
            }
        );
    }

    /**
     * Prepare a full Drush command, to include executable and alias.
     *
     * @param string $cmd
     *   The Drush command, without executable and alias, e.g. "pml". The arguments should be escaped.
     *
     * @return string
     *   The prepared Drush command to run, complete with executable and alias.
     */
    protected function prepareDrushCommand($cmd)
    {
        $baseCmd = sprintf("drush -y %s", escapeshellarg($this->alias));
        $cmd = "$baseCmd $cmd";
        Debug::debug($cmd);
        return $cmd;
    }

    /**
     * @param string $text
     *   Create a new message to display.
     *
     * @return Message
     *   The new Message.
     */
    protected function message($text = '')
    {
        return new Message($text, $this->output);
    }
}
