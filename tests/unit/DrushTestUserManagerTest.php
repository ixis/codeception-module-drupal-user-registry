<?php

use \Codeception\Lib\Console\Message;
use \Codeception\Util\Fixtures;
use \Codeception\Module\Drupal\UserRegistry\DrushTestUserManager;

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
     * Objects of this class should be instantiable.
     *
     * @test
     */
    public function shouldBeInstantiatable()
    {
        $this->assertInstanceOf(
            '\Codeception\Module\Drupal\UserRegistry\DrushTestUserManager',
            new DrushTestUserManager(Fixtures::get("validModuleConfig"))
        );
    }

    /**
     * An exception should be thrown when instantiating this class with an empty configuration.
     *
     * @test
     */
    public function testIfExceptionThrownWhenConfigurationIsEmpty()
    {
        $this->setExpectedException(
            '\Codeception\Exception\Configuration',
            "Please configure the drush-alias setting in your suite configuration."
        );
        new DrushTestUserManager(array());
    }

    /**
     * A "almost valid" configuration object WITHOUT the drush-alias value set should throw an exception.
     *
     * @test
     */
    public function testIfExceptionThrownWhenConfigurationIsMissingDrushAlias()
    {
        $this->setExpectedException(
            '\Codeception\Exception\Configuration',
            "Please configure the drush-alias setting in your suite configuration."
        );
        new DrushTestUserManager(Fixtures::get("invalidModuleConfig"));
    }

    /**
     * Test message().
     *
     * @test
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
        $refMethod = self::getProtectedMethod("message");
        $testUserManager = new DrushTestUserManager(Fixtures::get("validModuleConfig"));

        $expected = new Codeception\Lib\Console\Message($message, $output);
        $actual = $refMethod->invokeArgs($testUserManager, array($message));
        $this->assertInstanceOf('\Codeception\Lib\Console\Message', $actual);

        // Note we're only comparing the string-converted object as the $stream member variable is different between
        // instances.
        $this->assertEquals($expected->__toString(), $actual->__toString());
        $this->assertEquals($message, $actual->__toString());
    }

    /**
     * Get a protected method of a class via ReflectionClass.
     *
     * @param string $name
     *   The name of the protected method.
     *
     * @return ReflectionMethod
     */
    protected static function getProtectedMethod($name)
    {
        $class = new ReflectionClass('\Codeception\Module\Drupal\UserRegistry\DrushTestUserManager');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}
