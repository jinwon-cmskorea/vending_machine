<?php
/**
 * @see InfoDrink.php
 */
require_once __DIR__ .  '/InfoDrink.php';
/**
 * 사이다 클래스
 * InfoDrink 를 상속받음
 */
class Sprite extends InfoDrink {
    /**
     * 상품 번호
     * @var int
     */
    protected $_productNum = 1;
    
    /**
     * 상품 가격
     * @var int
     */
    protected $_price = 800;
}