<?php

use \Codeception\Module\Drupal\UserRegistry\Storage\ModuleConfigStorage;
use \Codeception\Util\Fixtures;

/**
 * Unit tests for DrushTestUserManager class.
 */
class ModuleConfigStorageTest extends \Codeception\TestCase\Test
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
    public function instantiateClass()
    {
        $this->assertInstanceOf(
            '\Codeception\Module\Drupal\UserRegistry\Storage\ModuleConfigStorage',
            new ModuleConfigStorage(Fixtures::get("validModuleConfig"))
        );
    }

    /**
     * @expectedException \Codeception\Exception\Configuration
     */
    public function testRootUserCannotBeDefinedMoreThanOnce()
    {
        $config = array(
            "users" => array(
                "root" => array(
                    "name" => "test.root",
                    "email" => "test.root@example.com",
                    "root" => true,
                    "roles" => array(),
                ),
                "root2" => array(
                    "name" => "test.root2",
                    "email" => "test.root2@example.com",
                    "root" => true,
                    "roles" => array(),
                ),
            ),
        );

        $config = new ModuleConfigStorage($config);
    }
}
