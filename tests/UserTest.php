<?php 
/**
 * @see User.php
 */
require_once __DIR__ . '/../library/User.php';
/**
 * User 테스트를 위한 클래스
 * UserTestClass
 */ 
class UserTestClass extends User { };

/**
 * User test case.
 */
class UserTest extends PHPUnit_Framework_TestCase {
    
    /**
     *
     * @var UserTestClass
     */
    private $user;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp() {
        parent::setUp();
        $this->user = new UserTestClass("ADMIN");
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown() {
        parent::tearDown();
    }
    
    /**
     * Tests User->__construct()
     */
    public function testGetRole() {
        $res = $this->user->getRole();
        $this->assertEquals("ADMIN", $res);
    }
}