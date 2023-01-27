<?php
/**
 * @see InfoDrink.php
 */
require_once __DIR__ .  '/InfoDrink.php';
/**
 * 주스 클래스
 * InfoDrink 를 상속받음
 */
class Juice extends InfoDrink {
    /**
     * @brief 상품 번호
     * @var int
     */
    protected $_productNum = 2;
    
    /**
     * @brief 상품 가격
     * @var int
     */
    protected $_price = 700;
    
    /**
     * 부모 메소드를 public 으로 바꿔서 호출
     * @return number
     */
    public function getProductNum() {
        return parent::_getProductNum();
    }
    
    /**
     * 부모 메소드를 public 으로 바꿔서 호출
     * @return number
     */
    public function getPrice() {
        return parent::_getPrice();
    }
}