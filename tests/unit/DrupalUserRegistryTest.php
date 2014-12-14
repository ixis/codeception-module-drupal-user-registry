<?php

use \Codeception\Module\Drupal\UserRegistry\DrupalTestUser;
use \Codeception\Module\DrupalUserRegistry;
use \Codeception\Util\Fixtures;

/**
 * Unit tests for DrupalUserRegistry class.
 *
 * This class only covers non-API or protected/private method tests. Tests for the 'public API' methods are included
 * in DrupalUserRegistryApiTest.php
 */
class DrupalUserRegistryTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     *   Store the Actor object being used to test.
     */
    protected $tester;

    /**
     * @var \Codeception\Module\DrupalUserRegistry
     *   Store any instance of the module being tested.
     */
    protected $module;

    /**
     * Test set-up.
     */
    public function _before()
    {
        $this->module = new DrupalUserRegistry();
    }
    /**
     * Objects of this class should be instantiable.
     *
     * @test
     */
    public function shouldBeInstantiatable()
    {
        $this->assertInstanceOf('\Codeception\Module\DrupalUserRegistry', $this->module);
    }

    /**
     * This class should extend \Codeception\Module
     *
     * @test
     */
    public function shouldExtendCodeceptionModule()
    {
        $this->assertInstanceOf('\Codeception\Module', $this->module);
    }
}
