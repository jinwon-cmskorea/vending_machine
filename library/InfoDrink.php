<?php
/**
 * 음료수 정보 클래스
 */
abstract class InfoDrink {
    /**
     * 상품 번호
     * @var int
     */
    protected $_productNum;
    
    /**
     * 상품 가격
     * @var int
     */
    protected $_price;
    
    /**
     * 상품 번호를 가져오는 메소드
     * @return int
     */
    
    public function getProductNum() {
        return $this->_productNum;
    }
    
    /**
     * 상품 가격을 가져오는 메소드
     * @return int
     */
    public function getPrice() {
        return $this->_price;
    }
}