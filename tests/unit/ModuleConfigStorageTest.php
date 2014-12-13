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
    public function shouldBeInstantiatable()
    {
        $this->assertInstanceOf(
            '\Codeception\Module\Drupal\UserRegistry\Storage\ModuleConfigStorage',
            new ModuleConfigStorage(Fixtures::get("validModuleConfig"))
        );
    }
}
