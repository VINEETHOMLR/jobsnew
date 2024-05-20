<?php

/**
 * @author 
 * @desc To describe an example of Model
 */

namespace src\models;

use inc\Raise;
use src\lib\Database;
use src\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use src\lib\Helper as H;
use src\lib\CoinClass;
use src\models\SiteData;

/**
 * @author 
 */
class UserExecutedDealHistory extends Database {

    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'deal_order_id';
    /**
     * Constructor of the model
     */

    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "user_executed_deal_history";

        $this->columns = [
            'deal_order_id',           
            'user_id',      
            'deal_id',      
            'side',    
            'role',       
            'executed_price',
            'amount',   
            'deal_price',   
            'fee',       
            'market',      
            'time',   
            'created_at',      
        ];
    }

    /**
     *
     * @return Array
    */
    public static function attrs()
    {
        return  [
                    'deal_order_id',           
                    'user_id',      
                    'deal_id',      
                    'side',    
                    'role',       
                    'executed_price',
                    'amount',   
                    'deal_price',   
                    'fee',       
                    'market',      
                    'time',   
                    'created_at', 
                ];
    }

    /**
     *
     * @return $this
     */
    public function assignAttrs($attrs = [])
    {   
        $isExternal = !empty($attrs);
        foreach (($isExternal ? $attrs : self::attrs()) as $eAttr => $attr) {
            $aAttr = $isExternal ? $eAttr : $attr;
           $this->{$aAttr} = $isExternal ? $attr : "";
        }
        
        return $this;
    }


    /**
     *
     * @param INT $pk
     */
    public function findByPK($pk)
    {
        $dtAry = parent::findByPK($pk);
        foreach ($dtAry as $attr => $val) {
            $this->{$attr} = $val;
        }
        return $this;
    }

   
    /**
     *
     * @return attrs data array
     */
    public function convertArray()
    {
        $temp = array();
        $attrs = $this->attrs();
        foreach ($attrs as $key) {
            $temp[$key] = isset($this->{$key})?$this->{$key}:'';
        }
        return $temp;
    }

    public function insert($ip) {

        $deal_order_id      = isset($ip['deal_order_id']) ? $ip['deal_order_id'] : 0;
        $user_id            = isset($ip['user_id']) ? $ip['user_id'] : 0;
        $deal_id            = isset($ip['deal_id']) ? $ip['deal_id'] : 0;
        $market             = isset($ip['market']) ? $ip['market'] : '';
        $side               = isset($ip['side']) ? $ip['side'] : 0;
        $role               = isset($ip['role']) ? $ip['role'] : 0;
        $executed_price     = isset($ip['executed_price']) ? $ip['executed_price'] : 0;
        $amount             = isset($ip['amount']) ? $ip['amount'] : 0;
        $deal_price         = isset($ip['deal_price']) ? $ip['deal_price'] : "";
        $fee                = isset($ip['fee']) ? $ip['fee'] : 0;
        $order_time         = isset($ip['order_time']) ? $ip['order_time'] : 0;
        $createtime         = time();
        

        $query = "INSERT INTO $this->tableName (`deal_order_id`,`user_id`,`deal_id`,`side`,`role`,`executed_price`,`amount`,`deal_price`,`fee`,`market`,`time`,`created_at`) VALUES (:deal_order_id,:user_id,:deal_id,:side,:role,:executed_price,:amount,:deal_price,:fee,:market,:order_time,:createtime)";

        $this->query($query);
        $this->bind(':deal_order_id', $deal_order_id);
        $this->bind(':user_id', $user_id);
        $this->bind(':deal_id', $deal_id);
        $this->bind(':side', $side);
        $this->bind(':role', $role);
        $this->bind(':executed_price', $executed_price);
        $this->bind(':amount', $amount);
        $this->bind(':deal_price', $deal_price);
        $this->bind(':fee', $fee);
        $this->bind(':market', $market);
        $this->bind(':order_time', $order_time);
        $this->bind(':createtime', $createtime);
       

        $this->execute();

        return true;
    }

    public function checkIdExists($deal_id){

        $deal_id = $this->callsql("SELECT deal_id FROM $this->tableName WHERE deal_id = '$deal_id' ","value");

        if(!empty($deal_id)){
            return $deal_id;
        } 

        return 0;
    }

    public function getAll($deal_order_id,$ip){

        $sql = $where_str = $select = '';
        $where_str_array = array();

        $where_str_array[] = 'deal_order_id=\''.$deal_order_id.'\'';

        $select = '*';
        if (!empty($ip['select'])) {
            if (is_array($ip['select'])) {
                $select = implode(',', $ip['select']);
            } else {
                $select = $ip['select'];
            }
        }

        $where_str = '1';
        if (!empty($where_str_array)) {
            $where_str = implode(' AND ', $where_str_array);
        }

        $page = "1";
        $perPage = "10";

        $pageStart = ($page - 1) * $perPage;

        $limit = ' LIMIT '.$pageStart.','.$perPage;

        $sql = 'SELECT '.$select.' FROM '.$this->tableName.' WHERE '.$where_str.' '.$limit.' ';
        
          
        $rows = $this->callsql($sql,"rows");

        if(empty($rows)){
           $rows = [];
        }

        return $rows;

    }

    

}
