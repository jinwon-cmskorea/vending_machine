<?php
/**
 * @see InfoDrink.php
 */
require_once __DIR__ .  '/InfoDrink.php';
/**
 * 콜라 클래스
 * InfoDrink 를 상속받음
 */
class Cola extends InfoDrink {
    /**
     * 상품 번호
     * @var int
     */
    protected $_productNum = 0;
    
    /**
     * 상품 가격
     * @var int
     */
    protected $_price = 1000;
}