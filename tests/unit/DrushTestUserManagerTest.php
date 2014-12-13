<?php

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
}
