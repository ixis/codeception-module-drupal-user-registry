<?php

use \Codeception\Lib\Console\Message;
use Codeception\Module\Drupal\UserRegistry\DrupalTestUser;
use \Codeception\Module\Drupal\UserRegistry\Storage\ModuleConfigStorage;
use \Codeception\Module\Drupal\UserRegistry\DrushTestUserManager;
use \Codeception\Util\Fixtures;
use \Codeception\Util\Stub;

/**
 * Unit tests for DrushTestUserManager class.
 */
class DrushTestUserManagerTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     *   Store the Actor object being used to test.
     */
    protected $tester;

    /**
     * @var ModuleConfigStorage
     *   A dummy storage object used in tests.
     */
    protected $storage;

    /**
     * Create a dummy ModuleConfigStorage object to use when instantiating DrushTestUserManager.
     */
    public function _before()
    {
        $this->storage = Stub::make('\Codeception\Module\Drupal\UserRegistry\Storage\ModuleConfigStorage');
    }

    /**
     * Objects of this class should be instantiable.
     *
     * @test
     */
    public function instantiateClass()
    {
        $this->assertInstanceOf(
            '\Codeception\Module\Drupal\UserRegistry\DrushTestUserManager',
            new DrushTestUserManager(Fixtures::get("validModuleConfig"), $this->storage)
        );
    }

    /**
     * An exception should be thrown when instantiating this class with an empty configuration.
     */
    public function testIfExceptionThrownWhenConfigurationIsEmpty()
    {
        $this->setExpectedException(
            '\Codeception\Exception\Configuration',
            "Please configure the drush-alias setting in your suite configuration."
        );
        new DrushTestUserManager(array(), $this->storage);
    }

    /**
     * A "almost valid" configuration object WITHOUT the drush-alias value set should throw an exception.
     */
    public function testIfExceptionThrownWhenConfigurationIsMissingDrushAlias()
    {
        $this->setExpectedException(
            '\Codeception\Exception\Configuration',
            "Please configure the drush-alias setting in your suite configuration."
        );
        new DrushTestUserManager(Fixtures::get("invalidModuleConfig"), $this->storage);
    }

    /**
     * Test message().
     *
     * @group protected
     */
    public function testMessage()
    {
        // Test we receive the expected object with both a message and no message (empty string).
        foreach (["", "This is the message to output."] as $message) {
            $this->messageWithString($message);
        }
    }

    /**
     * Helper function for testMessage()
     *
     * @param string $message
     *   The message to test with.
     */
    protected function messageWithString($message)
    {
        // Set up.
        $output = new Codeception\Lib\Console\Output(array());
        $refMethod = \Codeception\Module\UnitHelper::getNonPublicMethod(
            '\Codeception\Module\Drupal\UserRegistry\DrushTestUserManager',
            "message"
        );
        $testUserManager = new DrushTestUserManager(Fixtures::get("validModuleConfig"), $this->storage);

        $expected = new Codeception\Lib\Console\Message($message, $output);
        $actual = $refMethod->invokeArgs($testUserManager, array($message));
        $this->assertInstanceOf('\Codeception\Lib\Console\Message', $actual);

        // Note we're only comparing the string-converted object as the $stream member variable is different between
        // instances.
        $this->assertEquals($expected->__toString(), $actual->__toString());
        $this->assertEquals($message, $actual->__toString());
    }

    /**
     * Test prepareDrushCommand()
     */
    public function testPrepareDrushCommand()
    {
        $testUserManager = new DrushTestUserManager(Fixtures::get("validModuleConfig"), $this->storage);
        $refMethod = \Codeception\Module\UnitHelper::getNonPublicMethod(
            '\Codeception\Module\Drupal\UserRegistry\DrushTestUserManager',
            "prepareDrushCommand"
        );
        $this->assertEquals(
            "drush -y '@d7.local' st",
            $refMethod->invokeArgs($testUserManager, array("st")),
            "Returned prepared command was not as expected."
        );

        // @todo prepareDrushCommand() should really throw an exception if $cmd is empty.
        $this->assertEquals(
            "drush -y '@d7.local' ",
            $refMethod->invokeArgs($testUserManager, array("")),
            "Returned prepared command was not as expected."
        );
    }

    /**
     * Test deleteUser() escapes the username argument to the drush command.
     *
     * @see https://github.com/ixis/codeception-module-drupal-user-registry/issues/15
     */
    public function testDeleteUserEscapesUserArgumentToDrushCommand()
    {
        $mockStorage = $this->getMockBuilder('Codeception\Module\Drupal\UserRegistry\Storage\StorageInterface')
            ->getMock();

        $mock = $this->getMockBuilder('\Codeception\Module\Drupal\UserRegistry\DrushTestUserManager')
            ->setConstructorArgs(
                array(
                    array('drush-alias' => 'dummy'),
                    $mockStorage
                )
            )
            ->setMethods(array("runDrush"))
            ->getMock();

        $mock->expects($this->once())
            ->method("runDrush")
            ->with($this->matchesRegularExpression('/(["\'])a user name\1/'));

        $user = new DrupalTestUser("a user name", "password");

        $mock->deleteUser($user);
    }
}
