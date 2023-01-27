<?php 
/**
 * @see InfoDrink.php
 */
require_once __DIR__ . '/../library/InfoDrink.php';

/**
 * InfoDrink 테스트를 위한 클래스
 * InfoDrinkTestClass
 */ 
class InfoDrinkTestClass extends InfoDrink {
    public function getProductNum() {
        return parent::_getProductNum();
    }
    
    public function getPrice() {
        return parent::_getPrice();
    }
};

class ColaTestClass extends InfoDrinkTestClass {
    protected $_productNum = 0;
    
    protected $_price = 1000;
};

class SpriteTestClass extends InfoDrinkTestClass {
    protected $_productNum = 1;
    
    protected $_price = 800;
};

class JuiceTestClass extends InfoDrinkTestClass {
    protected $_productNum = 0;
    
    protected $_price = 700;
};
/**
 * InfoDrink test case.
 */
class InfoDrinkTest extends PHPUnit_Framework_TestCase {
    
    /**
     *
     * @var InfoDrinkTestClass
     */
    private $cola;
    
    
    /**
     *
     * @var InfoDrinkTestClass
     */
    private $sprite;
    
    
    /**
     *
     * @var InfoDrinkTestClass
     */
    private $juice;
    
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp() {
        parent::setUp();
        $this->cola = new ColaTestClass();
        $this->sprite = new SpriteTestClass();
        $this->juice = new JuiceTestClass();
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown() {
        parent::tearDown();
    }
    
    
    /**
     * Tests InfoDrink->getProductNum()
     */
    public function testGetProductNum() {
        $this->assertEquals(0, $this->cola->getProductNum());
        $this->assertEquals(1000, $this->cola->getPrice());
        
        $this->assertEquals(1, $this->sprite->getProductNum());
        $this->assertEquals(800, $this->sprite->getPrice());
        
        $this->assertEquals(0, $this->juice->getProductNum());
        $this->assertEquals(700, $this->juice->getPrice());
    }
    
    /**
     * Tests InfoDrink->getPrice()
     */
    public function testGetPrice() {
        
    }
}