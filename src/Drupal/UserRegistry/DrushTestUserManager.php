<?php

namespace Codeception\Module\Drupal\UserRegistry;

use Codeception\Exception\Configuration as ConfigurationException;
use Codeception\Lib\Console\Message;
use Codeception\Lib\Console\Output;
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

        $cmdOutput = $this->runDrush("user-information " . escapeshellarg($user->name));

        if (count($cmdOutput) == 1
            && strpos(current($cmdOutput), "Could not find a uid for the search term '{$user->name}'!") !== false) {

            // Create the user.
            $this->message("Creating test user '{$user->name}' on '{$this->alias}'.", new Output(array()))->writeln();
            $this->runDrush(
                sprintf(
                    "user-create %s --mail=%s --password=%s",
                    escapeshellarg($user->name),
                    escapeshellarg("{$user->name}@example.com"),
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
        } else {
            Debug::debug("User {$user->name} already exists, skipping.");
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
     * Run a drush command.
     *
     * @param $cmd
     *   The drush command, without executable and alias.
     *   e.g. "pml"
     *   The arguments should be escaped.
     *
     * @return array
     *   Array of lines output from the drush command.
     */
    protected function runDrush($cmd)
    {
        $baseCmd = sprintf(
            "drush -y %s",
            escapeshellarg($this->alias)
        );

        $cmd = "$baseCmd $cmd";

        Debug::debug($cmd);

        $cmdOutput = array();
        exec($cmd, $cmdOutput);

        return array_filter(
            $cmdOutput,
            function ($line) {
                return strpos($line, "Warning: Permanently added") !== 0;
            }
        );
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
