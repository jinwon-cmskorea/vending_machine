<?php 
/**
 * @see InfoDrink
 */
require_once __DIR__ . '/../library/InfoDrink.php';

/**
* @see Dispenser
*/
require_once __DIR__ . '/../library/Dispenser.php';
/**
 * @see User
 */
require_once __DIR__ . '/../library/User.php';
/**
 * Dispenser 테스트를 위한 클래스
 * DispenserTestClass
 */
class DispenserTestClass extends Dispenser {
    public function __construct(array $maxStock) {
        parent::__construct($maxStock);
    }
    
    public function giveCoins($money) {
        return parent::_giveCoins($money);
    }
    
    public function isAdmin($role) {
        return parent::_isAdmin($role);
    }
    
    public function alert($status) {
        return parent::_alert($status);
    }
    
    public function getRemainCharge() {
        return $this->_remainCharge;
    }
    
    public function getInsertedmoney() {
        return $this->_insertedMoney;
    }
    
    public function getMaxStock() {
        return $this->_maxStock;
    }
    
    public function getRemainStock() {
        return $this->_remainStock;
    }
    
    public function getAllMoney() {
        return $this->_allMoney;
    }
    
    public function setRemainCharge($changeCharge) {
        $this->_remainCharge = $changeCharge;
    }
    
    public function insertAllMoney($productNum, $count) {
        $this->_allMoney += $this->_infoDrinks[$productNum] * $count;
    }
};

/**
 * InfoDrink 테스트를 위한 클래스
 * InfoDrinkTestClass
 */
class InfoDrinkTestClass extends InfoDrink {};

class ColaTestClass extends InfoDrinkTestClass {
    protected $_productNum = 0;
    
    protected $_price = 1000;
};

class SpriteTestClass extends InfoDrinkTestClass {
    protected $_productNum = 1;
    
    protected $_price = 800;
};

class JuiceTestClass extends InfoDrinkTestClass {
    protected $_productNum = 2;
    
    protected $_price = 700;
};

/**
 * Dispenser test case.
 */
class DispenserTest extends PHPUnit_Framework_TestCase {
    /**
     *
     * @var DispenserTestClass
     */
    private $dispenser;
    
    /**
     *
     * @var InfoDrinkTestClass
     */
    private $infoDrink;
    /**
     *
     * @var ColaTestClass
     */
    private $cola;
    /**
     *
     * @var SpriteTestClass
     */
    private $sprite;
    /**
     *
     * @var JuiceTestClass
     */
    private $juice;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp() {
        parent::setUp();
        $maxStock = array(
            0   => 10,
            1   => 10,
            2   => 10
        );
        $this->dispenser = new DispenserTestClass($maxStock);
        $this->infoDrink = new InfoDrinkTestClass();
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
     * Tests Dispenser->__construct()
     */
    public function test__construct() {
        $maxStock = array(
            0   => 10,
            1   => 10,
            2   => 10
        );
        $this->dispenser->__construct($maxStock);
    }
    
    /**
     * Tests Dispenser->_giveCoins()
     */
    public function testGiveCoins() {
        $this->dispenser->insertMoney(3000);
        $this->dispenser->insertAllMoney($this->cola->getProductNum(), 2);
        
        $expect1 = "1000 원 1 개 반환되었습니다.";
        $this->assertEquals($expect1, $this->dispenser->giveCoins($this->dispenser->getInsertedmoney() - $this->dispenser->getAllMoney()));
        $remainCoins = $this->dispenser->getRemainCharge();
        $this->assertEquals(9, $remainCoins['1000']);
        
        $expect2 = "1000 원 9 개 500 원 2 개 반환되었습니다.";
        $this->assertEquals($expect2, $this->dispenser->giveCoins(10000));
        $remainCoins2 = $this->dispenser->getRemainCharge();
        $this->assertEquals(0, $remainCoins2['1000']);
        $this->assertEquals(8, $remainCoins2['500']);
    }
    
    /**
     * Tests Dispenser->_giveCoins() 거슬러줄 화폐가 부족한 경우
     */
    public function testGiveCoinsNotEnoughCoins() {
        $testDis = clone $this->dispenser;
        $changeCharge = array(
            '1000'  => 2,
            '500'   => 0,
            '100'   => 0
        );
        $testDis->setRemainCharge($changeCharge);
        
        $user = new User(Dispenser::ADMIN);
        $testDis->fillProduct($this->sprite->getProductNum(), 5, $user->getRole());
        $testDis->insertMoney(2000);
        $testDis->insertAllMoney($this->sprite->getProductNum(), 2);
        
        $expect = "반환할 거스름 돈이 부족합니다. 투입금액 2000 원이 반환되었습니다.";
        $this->assertEquals($expect, $testDis->giveCoins($testDis->getInsertedmoney() - $testDis->getAllMoney()));
        $remainStock = $testDis->getRemainStock();
        $this->assertEquals(5, $remainStock[$this->sprite->getProductNum()]);//주문이 취소됐으므로 재고도 되돌려져야함
        
    }
    
    /**
     * Tests Dispenser->insertMoney()
     */
    public function testInsertMoney() {
        $res = $this->dispenser->insertMoney(1000);
        $this->assertEquals(1000, $res);
        
        $res = $this->dispenser->insertMoney(800);
        $this->assertEquals(1800, $res);
    }
    
    /**
     * Tests Dispenser->insertMoney() 남아있는 거스름돈보다 더 많이 투입한 경우
     */
    public function testInsertMoneyNotEnoughCharge() {
        $expect = "남아있는 거스름돈 금액보다 투입 금액이 더 많습니다. 17000 원이 반환되었습니다.";
        try {
            $this->dispenser->insertMoney(17000);
            $this->assertFalse(true);
        } catch (Exception $e) {
            $this->assertEquals($expect, $e->getMessage());
            $this->assertEquals(0, $this->dispenser->getInsertedmoney());
        }
        
        $expect2 = "남아있는 거스름돈 금액보다 투입 금액이 더 많습니다. 18000 원이 반환되었습니다.";
        try {
            $this->dispenser->insertMoney(7000);
            $this->dispenser->insertMoney(8000);
            $this->dispenser->insertMoney(3000);
            $this->assertFalse(true);
        } catch (Exception $e) {
            $this->assertEquals($expect2, $e->getMessage());
            $this->assertEquals(0, $this->dispenser->getInsertedmoney());
        }
    }
    
    /**
     * Tests Dispenser->fillProduct()
     */
    public function testFillProduct() {
        $user = new User(Dispenser::ADMIN);
        
        $res1 = array(
            0   => 0,
            1   => 0,
            2   => 5
        );
        $this->dispenser->fillProduct($this->juice->getProductNum(), 5, $user->getRole());
        $remainStack = $this->dispenser->getRemainStock();
        $this->assertEquals(5, $remainStack[$this->juice->getProductNum()]);
        $this->assertEquals($res1, $remainStack);
        
        $res2 = array(
            0   => 10,
            1   => 0,
            2   => 5
        );
        $this->dispenser->fillProduct($this->cola->getProductNum(), 12, $user->getRole());
        $remainStack2 = $this->dispenser->getRemainStock();
        $this->assertEquals(10, $remainStack2[$this->cola->getProductNum()]);
        $this->assertEquals($res2, $remainStack2);
    }
    
    /**
     * Tests Dispenser->fillProduct() 관리자가 아닐 경우
     */
    public function testFillProductNotAdmin() {
        $user = new User(Dispenser::USER);
        
        try {
            $this->dispenser->fillProduct($this->juice->getProductNum(), 5, $user->getRole());
            $this->assertFalse(true);
        } catch (Exception $e) {
            $this->assertEquals("자판기 관리자가 아닙니다.", $e->getMessage());
        }
    }
    
    /**
     * Tests Dispenser->fillCoins()
     */
    public function testfillCoins() {
        $user = new User(Dispenser::ADMIN);
        
        $expect1 = array(
            '1000'  => 15,
            '500'   => 10,
            '100'   => 10
        );
        $this->assertEquals($expect1, $this->dispenser->fillCoins(1000, 5, $user->getRole()));
        
        $expect2 = array(
            '1000'  => 15,
            '500'   => 10,
            '100'   => 20
        );
        $this->assertEquals($expect2, $this->dispenser->fillCoins(100, 10, $user->getRole()));
    }
    
    /**
     * Tests Dispenser->fillCoins() 관리자가 아닐 경우
     */
    public function testfillCoinsNotAdmin() {
        $user = new User(Dispenser::USER);
        
        try {
            $this->dispenser->fillCoins(1000, 5, $user->getRole());
            $this->assertFalse(true);
        } catch (Exception $e) {
            $this->assertEquals("자판기 관리자가 아닙니다." , $e->getMessage());
        }
    }
    
    /**
     * Tests Dispenser->fillCoins() 사용하는 거스름돈 화폐가 아닌 경우
     */
    public function testfillCoinsNotUseGives() {
        $user = new User(Dispenser::ADMIN);
        
        try {
            $this->dispenser->fillCoins(2000, 5, $user->getRole());
            $this->assertFalse(true);
        } catch (Exception $e) {
            $this->assertEquals("사용하는 거스름돈 화폐가 아닙니다." , $e->getMessage());
        }
    }
    
    /**
     * Tests Dispenser->changeMaxStock()
     */
    public function testChangeMaxStock() {
        $user = new User(Dispenser::ADMIN);
        
        $res1 = array(
            0   => 20,
            1   => 10,
            2   => 10
        );
        $this->dispenser->changeMaxStock($this->cola->getProductNum(), 20, $user->getRole());
        $maxStock = $this->dispenser->getMaxStock();
        $this->assertEquals(20, $maxStock[$this->cola->getProductNum()]);
        $this->assertEquals($res1, $maxStock);
        
        $res2 = array(
            0   => 20,
            1   => 10,
            2   => 5
        );
        $this->dispenser->changeMaxStock($this->juice->getProductNum(), 5, $user->getRole());
        $maxStock2 = $this->dispenser->getMaxStock();
        $this->assertEquals(5, $maxStock2[$this->juice->getProductNum()]);
        $this->assertEquals($res2, $maxStock2);
    }
    
    /**
     * Tests Dispenser->changeMaxStock() 관리자가 아닐 경우
     */
    public function testChangeMaxStockNotAdmin() {
        $user = new User(Dispenser::USER);
        
        try {
            $this->dispenser->changeMaxStock($this->cola->getProductNum(), 20, $user->getRole());
            $this->assertFalse(true);
        } catch (Exception $e) {
            $this->assertEquals("자판기 관리자가 아닙니다.", $e->getMessage());
        }
    }
    
    /**
     * Tests Dispenser->changeMaxStock() 최대 재고량을 현재 재고량보다 적게 설정하려는 경우
     */
    public function testChangeMaxStockLowRemainStock() {
        $user = new User(Dispenser::ADMIN);
        
        try {
            $this->dispenser->fillProduct($this->cola->getProductNum(), 10, $user->getRole());
            $this->dispenser->changeMaxStock($this->cola->getProductNum(), 5, $user->getRole());
            $this->assertFalse(true);
        } catch (Exception $e) {
            $this->assertEquals("현재 재고량보다 적게 설정할 수 없습니다.", $e->getMessage());
        }
    }
    
    /**
     * Tests Dispenser->_isAdmin()
     */
    public function testIsAdmin() {
        $res = $this->dispenser->isAdmin('ADMIN');
        $this->assertTrue($res);
        
        $res2 = $this->dispenser->isAdmin('USER');
        $this->assertFalse($res2);
    }
    
    /**
     * Tests Dispenser->_alert()
     */
    public function testAlert() {
        $res1 = $this->dispenser->alert(Dispenser::NOT_ADMIN);
        $this->assertEquals($res1, "자판기 관리자가 아닙니다.");
        
        $res2 = $this->dispenser->alert(Dispenser::NOT_ENOUGH_MONEY);
        $this->assertEquals($res2, "투입 금액이 부족합니다.");
    }
    
    /**
     * Tests Dispenser->_getProduct()
     */
    public function testGetProduct() {
        $user = new User(Dispenser::ADMIN);

        $this->dispenser->fillProduct($this->cola->getProductNum(), 10, $user->getRole());
        $this->dispenser->fillProduct($this->sprite->getProductNum(), 10, $user->getRole());
        
        $expect1 = "0 상품을 3 만큼 주문하셨습니다.\n";
        $expect1 .= "1000 원 1 개 반환되었습니다.";
        $this->dispenser->insertMoney(4000);
        $res1 = $this->dispenser->getProduct($this->cola->getProductNum(), 3);
        $this->assertEquals($expect1, $res1);
        $remain1 = $this->dispenser->getRemainStock();
        $this->assertEquals(7, $remain1[$this->cola->getProductNum()]);
        $coinRemain1 = $this->dispenser->getRemainCharge();
        $this->assertEquals(9, $coinRemain1['1000']);
        
        $expect2 = "1 상품을 1 만큼 주문하셨습니다.\n";
        $expect2 .= "100 원 2 개 반환되었습니다.";
        $this->dispenser->insertMoney(1000);
        $res2 = $this->dispenser->getProduct($this->sprite->getProductNum(), 1);
        $this->assertEquals($expect2, $res2);
        $remain2 = $this->dispenser->getRemainStock();
        $this->assertEquals(9, $remain2[$this->sprite->getProductNum()]);
        $coinRemain2 = $this->dispenser->getRemainCharge();
        $this->assertEquals(8, $coinRemain2['100']);
        
        $expect3 = "0 상품을 4 만큼 주문하셨습니다.\n";
        $expect3 .= "투입 금액과 주문 금액이 동일해 반환되는 거스름돈이 없습니다.";
        $this->dispenser->insertMoney(4000);
        $res3 = $this->dispenser->getProduct($this->cola->getProductNum(), 4);
        $this->assertEquals($expect3, $res3);
        $remain3 = $this->dispenser->getRemainStock();
        $this->assertEquals(3, $remain3[$this->cola->getProductNum()]);
        $coinRemain3 = $this->dispenser->getRemainCharge();
        $this->assertEquals(9, $coinRemain3['1000']);
    }
    
    /**
     * Tests Dispenser->_getProduct() 재고가 부족할 경우
     */
    public function testGetProductNotEnoughProduct() {
        $user = new User(Dispenser::ADMIN);
        $this->dispenser->fillProduct($this->cola->getProductNum(), 2, $user->getRole());
        
        $expect1 = "재고량이 부족한 상품입니다. 주문한 상품의 현재 재고량은 2 입니다.";
        
        try {
            $this->dispenser->insertMoney(4000);
            $res1 = $this->dispenser->getProduct($this->cola->getProductNum(), 3);
            $this->assertFalse(true);
        } catch (Exception $e) {
            $this->assertEquals($expect1, $e->getMessage());
        }
    }
    
    /**
     * Tests Dispenser->_getProduct() 투입 금액이 부족할 경우
     */
    public function testGetProductNotEnoughInsertMoney() {
        $user = new User(Dispenser::ADMIN);
        $this->dispenser->fillProduct($this->cola->getProductNum(), 10, $user->getRole());
        
        $expect1 = "투입 금액이 부족합니다.";
        
        try {
            $this->dispenser->insertMoney(2000);
            $res1 = $this->dispenser->getProduct($this->cola->getProductNum(), 3);
            $this->assertFalse(true);
        } catch (Exception $e) {
            $this->assertEquals($expect1, $e->getMessage());
        }
    }
    
    /**
     * Tests Dispenser->_getProduct() 거스름돈 재고가 부족한 경우
     */
    public function testGetProductNotEnoughCharge() {
        $testDis = clone $this->dispenser;
        $changeCharge = array(
            '1000'  => 2,
            '500'   => 0,
            '100'   => 0
        );
        $testDis->setRemainCharge($changeCharge);
        
        $user = new User(Dispenser::ADMIN);
        $testDis->fillProduct($this->sprite->getProductNum(), 5, $user->getRole());
        $testDis->insertMoney(2000);
        $res = $testDis->getProduct($this->sprite->getProductNum(), 2);
        $expect = "1 상품을 2 만큼 주문하셨습니다.\n";
        $expect .= "반환할 거스름 돈이 부족합니다. 투입금액 2000 원이 반환되었습니다.";
        $this->assertEquals($expect, $res);
        
        $remainStock = $testDis->getRemainStock();
        $this->assertEquals(5, $remainStock[$this->sprite->getProductNum()]);
    }
}