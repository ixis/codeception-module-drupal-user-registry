<?php

use \Codeception\Module\Drupal\UserRegistry\DrupalTestUser;
use \Codeception\Util\Fixtures;

/**
 * Unit tests for DrushTestUserManager class.
 */
class DrupalTestUserTest extends \Codeception\TestCase\Test
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
        $user = Fixtures::get("drupalTestUser");
        $this->assertInstanceOf(
            '\Codeception\Module\Drupal\UserRegistry\DrupalTestUser',
            new DrupalTestUser($user->name, $user->pass)
        );
    }

    /**
     * Test the class __toString() method.
     *
     * @test
     */
    public function testToString()
    {
        $user = new DrupalTestUser("Test Username", "password");
        $this->assertEquals("Test Username", $user->__toString());
        $this->assertEquals("Test Username", $user . "");
    }
}
