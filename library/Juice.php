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
}