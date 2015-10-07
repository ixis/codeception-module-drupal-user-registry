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
    public function instantiateClass()
    {
        $user = Fixtures::get("drupalTestUser");
        $this->assertInstanceOf(
            '\Codeception\Module\Drupal\UserRegistry\DrupalTestUser',
            new DrupalTestUser($user->name, $user->pass)
        );
    }

    /**
     * Test the public member variables are set correctly, and can be accessed.
     */
    public function testPublicMemberVariables()
    {
        $u = new DrupalTestUser("Test Username", "password");
        $this->assertEquals("Test Username", $u->name);
        $this->assertEquals("password", $u->pass);
        $this->assertNull($u->roleName);
        $this->assertNull($u->email);

        $u = new DrupalTestUser("Test Username", "password", "role");
        $this->assertEquals("role", $u->roleName);
        $this->assertNull($u->email);

        $u = new DrupalTestUser("Test Username", "password", "role", "email@example.com");
        $this->assertEquals("email@example.com", $u->email);
    }

    /**
     * Test the class __toString() method.
     */
    public function testToString()
    {
        $user = new DrupalTestUser("Test Username", "password");
        $this->assertEquals("Test Username", $user->__toString());
        $this->assertEquals("Test Username", $user . "");
    }
}
