<?php 
/**
 * 사용자 클래스
 */
class User {
    /**
     * @brief 사용자 역할을 저장하는 변수
     * @var string
     */
    protected $_role;
    
    /**
     * @brief 생성자
     * 어떤 역할을 저장할건지 입력받아서 멤버 변수에 저장
     * 
     * @param string $role 부여할 역할
     */
    public function __construct($role) {
        $this->_role = $role;
    }
    
    /**
     * @brief role 매개변수를 불러오는 메소드
     * 
     * @return string $_role
     */
    public function getRole() {
        return $this->_role;
    }
}