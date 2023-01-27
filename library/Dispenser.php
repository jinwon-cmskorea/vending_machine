<?php 
/**
 * @see Cola.php
 */
require_once __DIR__ .  '/Cola.php';
/**
 * @see Sprite.php
 */
require_once __DIR__ .  '/Sprite.php';
/**
 * @see Juice.php
 */
require_once __DIR__ .  '/Juice.php';
/*
 * 음료수 정보를 클래스로 가질 필요가 있는지 의문
 * 배열로 선언하는 게 깔끔한지, 클래스가 깔끔한지 알아봐야함
 */
/**
 * 자판기 클래스
 */
class Dispenser {
    /**
     * 투입 금액 부족 시 경고 메세지
     * @var string
     */
    const NOT_ENOUGH_MONEY = "투입 금액이 부족합니다.";
    
    /**
     * 자판기 관리자가 아닐 시 경고 메세지
     * @var string
     */
    const NOT_ADMIN = "자판기 관리자가 아닙니다.";
    /**
     * 관리자 명칭
     * @var string
     */
    const ADMIN = "ADMIN";
    /**
     * 유저 명칭
     * @var string
     */
    const USER = "USER";
    /**
     * 사용하는 거스름돈
     * @var array
     */
    const ALLOW_GIVES = array('1000', '500', '100');
    /**
     * 초기값 설정
     * @var int
     */
    const INIT_CHARGE = 10;
    /**
     * 남은 거스름돈 동전 갯수
     * @var array
     */
    protected $_remainCharge = array();
    
    /**
     * 사용자가 투입한 금액
     * @var int
     */
    protected $_insertedMoney = 0;
    
    /**
     * 각 상품별 최대 재고 갯수
     * @var array
     */
    protected $_maxStock = array();
    
    /**
     * 각 상품별 남아있는 재고 갯수
     * @var array
     */
    protected $_remainStock = array();
    
    /**
     * 총 금액
     * @var int
     */
    protected $_allMoney = 0;
    
    /**
     * 음료수 정보
     * @var array
     *      array(
     *          '콜라 클래스의 상품번호'      => '상품 가격',
     *          '사이다 클래스의 상품번호'    => '상품 가격',
     *          '주스 클래스의 상품번호'      => '상품 가격'
     *      )
     */
    protected $_infoDrinks = array();
    
    /**
     * 생성자
     * 각 상품별 최대 재고 갯수를 입력받아 멤버 변수에 저장
     * 
     * @param array $maxStock 각 상품별 재고가 들어있는 배열
     *        array (
     *              0       => 갯수,
     *              1       => 갯수,
     *              2       => 갯수
     *        )
     * @return void
     */
    protected function __construct(array $maxStock) {
        //입력받은 최대 재고량만큼 설정
        $this->_maxStock = $maxStock;
        //재고를 채우지 않았으므로 초기값 0으로 설정
        foreach ($maxStock as $key => $value) {
            $this->_remainStock[$key] = 0;
        }
        //초기 거스름돈 설정
        $this->_remainCharge = array(
            '1000'  => self::INIT_CHARGE,
            '500'   => self::INIT_CHARGE,
            '100'   => self::INIT_CHARGE
        );
        
        $cola = new Cola();
        $sprite = new Sprite();
        $juice = new Juice();
        //상품 번호 및 가격 설정
        $this->_infoDrinks = array(
            $cola->getProductNum()      => $cola->getPrice(),
            $sprite->getProductNum()    => $sprite->getPrice(),
            $juice->getProductNum()     => $juice->getPrice()
        );
    }
    
    /**
     * 거스름돈을 지폐 동전으로 반환하는 메소드
     * @param int $money 거스름돈
     * @return string
     */
    protected function _giveCoins($money) {
        $temp = 0;
        $str = "";
        $saveStatus = array(
            '1000'  => 0,
            '500'   => 0,
            '100'   => 0
        );
        if ($money == 0) {
            return "투입 금액과 주문 금액이 동일해 반환되는 거스름돈이 없습니다.";
        }
        
        foreach ($this->_remainCharge as $key => $value) {
            //$key 값 지폐,코인 몇개 반환해줘야하는 지 확인
            $temp = (int)floor($money / $key);
            //남아있는 거스름돈 보다 반환 갯수가 많은 경우, 남아있는 거스름돈 먼저 전부 반환
            if ($temp >= $this->_remainCharge[$key]) {
                $remain = $this->_remainCharge[$key];
                $this->_remainCharge[$key] = 0;//전부 반환했으로 0
                $saveStatus[$key] = $remain;//나중에 거스름돈 반환에 실패할 경우, 상태를 되돌려야 하므로 임시 저장
                $str .= "{$key} 원 {$remain} 개 ";
                $money = $money - ($key * $remain);
            } else if ($temp < $this->_remainCharge[$key] && $temp > 0){
                $this->_remainCharge[$key] -= $temp;
                $money -= $key * $temp;
                $saveStatus[$key] = $temp;
                $str .= "{$key} 원 {$temp} 개 ";
            }
        }
        $str .= "반환되었습니다.";
        
        //$money가 0이 아니라는 건 반환할 거스름돈이 부족하단 뜻
        if ($money != 0) {
            foreach ($saveStatus as $key => $value) {
                $this->_remainCharge[$key] += $value;
            }
            return "반환할 거스름 돈이 부족합니다. 투입금액 {$this->_insertedMoney} 원이 반환되었습니다.";
        } else {
            return $str;
        }
    }
    
    /**
     * 사용자로부터 투입된 금액을 받아 멤버 변수에 저장
     * @throws Exception 남아있는 거스름돈 보다 투입 금액이 더 큰 경우
     * @param int $money 사용자로부터 투입된 금액
     * @return int
     */
    public function insertMoney($money) {
        //현재 거스름돈 통에 얼마나 남아있는 지 구하기
        $remainCharge = 0;
        foreach ($this->_remainCharge as $key => $value) {
            $remainCharge += $key * $value;
        }
        
        $this->_insertedMoney += $money;
        if ($remainCharge < $this->_insertedMoney) {
            $temp = $this->_insertedMoney;
            $this->_insertedMoney = 0;
            throw new Exception("남아있는 거스름돈 금액보다 투입 금액이 더 많습니다. {$temp} 원이 반환되었습니다.");
        }
        
        return $this->_insertedMoney;
        /*
         * 굳이 boolean 을 반환해야하는지 의문
         * -> 현재까지 투입한 금액 출력해주는 거로 변경
         */
    }
    
    /**
     * 입력받은 상품번호의 재고를 채우는 메소드
     * 
     * @throws Exception 관리자가 아닐 경우 
     * @param int $productNum 상품번호
     * @param int $count 채울 양
     * @param string $role 유저 권한
     */
    public function fillProduct($productNum, $count, $role) {
        if (!$this->_isAdmin($role)) {
            throw new Exception($this->_alert(self::NOT_ADMIN));
        } else {
            if ($this->_remainStock[$productNum] + $count < $this->_maxStock[$productNum]) {
                $this->_remainStock[$productNum] += $count;
            } else {
                $this->_remainStock[$productNum] = $this->_maxStock[$productNum];
            }
        }
        
        return $this->_remainStock;
        /*
         * 현재 최대로 넣을 수 있는 재고량보다 많아질 경우, 최대 재고량으로 맞추고 있음.
         * 나중에 보완한다면 최대 재고값보다 넘치는 경우 남은 재고에 대한 처리가 필요함
         * (경고문 반환 또는 넘친 재고가 몇개인지 문자열 출력)
         * -> 매개변수로 관리자인 지 확인, 채워진 현재 재고량을 배열로 리턴
         */
    }
    
    /**
     * 입력받은 거스름돈용 지폐, 동전을 채우는 메소드
     *
     * @throws Exception 관리자가 아닐 경우
     * @param int $coin 채울 지폐 또는 동전
     * @param int $count 채울 양
     * @param string $role 유저 권한
     */
    public function fillCoins($coin, $count, $role) {
        if (!$this->_isAdmin($role)) {
            throw new Exception($this->_alert(self::NOT_ADMIN));
        } else if (!in_array($coin, self::ALLOW_GIVES)) {
            throw new Exception("사용하는 거스름돈 화폐가 아닙니다.");
        } else {
            $this->_remainCharge[$coin] += $count;
        }
        
        return $this->_remainCharge;
    }
    
    /**
     * 입력받은 상품의 최대 재고량을 바꾸는 메소드
     * 
     * @throws Exception 관리자가 아닐 경우
     *                   최대 재고량을 현재 재고량보다 적게 설정하려는 경우
     * 
     * @param int $productNum 상품 번호
     * @param int $count 바꿀 최대 재고량
     * 
     * @return int
     */
    public function changeMaxStock($productNum, $count, $role) {
        if (!$this->_isAdmin($role)) {
            throw new Exception($this->_alert(self::NOT_ADMIN));
        } else if ($count < $this->_remainStock[$productNum]) {
            throw new Exception("현재 재고량보다 적게 설정할 수 없습니다.");
        } else {
            $this->_maxStock[$productNum] = $count;
        }
        return $this->_maxStock;
        /*
         * 메소드 외부에서 관리자인지 확인하는 부분이 필요함.
         * 그래서 User 클래스에 role 을 가져오는 getter 추가가 필요해보임
         * 또한 내부 값만 바꾸는데, 리턴값까지 필요한지 의문
         * -> 매개변수로 관리자인 지 확인, 현재 최대 재고량을 배열로 리턴
         */
    }
    
    /**
     * @brief 관리자인지 확인하는 메소드
     * 
     * @param string $role 사용자의 역할
     * @return boolean
     */
    protected function _isAdmin($role) {
        if ($role != self::ADMIN) {
            return false;
        } else {
            return true;
        }
        /*
         * User 클래스의 role 변수를 확인하려면 public 으로 바꿔야 할듯
         * -> 매개변수로 $role을 받아온 뒤, 내부에서 사용하기로 결정
         */
    }
    
    /**
     * 입력된 상수에 따라 경고문을 출력하는 메소드
     * 
     * @param const string $status
     * @return string
     */
    protected function _alert($status) {
        if ($status == self::NOT_ADMIN) {
            return self::NOT_ADMIN;
        } else if ($status == self::NOT_ENOUGH_MONEY) {
            return self::NOT_ENOUGH_MONEY;
        }
        /*
         * 외부에서 관리자인 지 확인후, 경고문을 출력해줄거라면 public 해줘야함
         * 하지만 내부에서 돈이 충분한지만 확인한다면 protected 가 맞음
         */
    }
    
    /**
     * 사용자가 주문한 상품 결과를 출력해주는 메소드
     * 
     * @throws Exception 재고량보다 주문한 양이 많은 경우
     *                   주문한 양보다 투입 금액이 부족한 경우
     * @param int $productNum 상품번호
     * @param int $count 상품 갯수
     * @return string
     */
    public function getProduct($productNum, $count) {
        if ($this->_remainStock[$productNum] - $count < 0) {
            throw new Exception("재고량이 부족한 상품입니다. 주문한 상품의 현재 재고량은 {$this->_remainStock[$productNum]} 입니다.");
        } else if ($this->_infoDrinks[$productNum] * $count > $this->_insertedMoney) {
            throw new Exception($this->_alert(self::NOT_ENOUGH_MONEY));
        } else {
            $res = $productNum . " 상품을 " . $count . " 만큼 주문하셨습니다.\n";
            $this->_remainStock[$productNum] -= $count;
            $this->_allMoney += $this->_infoDrinks[$productNum] * $count;
            
            //상품 하나를 주문하면 바로 거스름돈 반환
            $res .= $this->_giveCoins($this->_insertedMoney - $this->_allMoney);
            //만약 반환할 거스름돈이 부족하다면 주문한 상품 재고를 되돌림
            if (strrpos($res, "부족합니다.")) {
                $this->_remainStock[$productNum] += $count;
            }
            /*
             * 투입금액 및 상품주문 금액 초기화
             */
            $this->_insertedMoney = 0;
            $this->_allMoney = 0;
        }

        return $res;
        /*
         * 의도한 동작은 이용자가 주문하면 돈 계산 후, 무엇을 얼마나 주문했는지 출력하는 메소드
         * 여러개 계산하면 이를 여러번 호출해줘야함.
         * 주문 결과를 저장하는 배열이 필요해보이고, 외부에서 호출 할 수 있게 public 으로 바꿔줘야할 듯?
         * -> 하나의 주문을 받은 후, 바로 거스름돈을 반환해주는 자판기로 바꿈
         */
    }
}