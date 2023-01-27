<?php
/**
 * 음료수 정보 클래스
 */
class InfoDrink {
    /**
     * @brief 상품 번호
     * @var int
     */
    protected $_productNum;
    
    /**
     * @brief 상품 가격
     * @var int
     */
    protected $_price;
    
    /**
     * @brief 상품 번호를 가져오는 메소드
     * @return int
     */
    
    protected function _getProductNum() {
        return $this->_productNum;
    }
    
    /**
     * @brief 상품 가격을 가져오는 메소드
     * @return int
     */
    protected function _getPrice() {
        return $this->_price;
    }
}