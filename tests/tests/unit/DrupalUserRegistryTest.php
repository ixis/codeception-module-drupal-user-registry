<?php

use \Codeception\Module\DrupalUserRegistry;

/**
 * Unit tests for DrupalUserRegistry class.
 */
class DrupalUserRegistryTest extends \Codeception\TestCase\Test
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
        $this->assertInstanceOf('\Codeception\Module\DrupalUserRegistry', new DrupalUserRegistry());
    }

    /**
     * This class should extend \Codeception\Module
     *
     * @test
     */
    public function shouldExtendCodeceptionModule()
    {
        $this->assertInstanceOf('\Codeception\Module', new DrupalUserRegistry());
    }
}
