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

/**
 * @author 
 */
class CoinPriceHistory extends Database {

    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */

    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "coin_price_history";

        $this->columns = [
            'id',            
            'coin_id',       
            'coin_code',     
            'price',         
            'percentage',    
            'diff',          
            'open_price',    
            'latest_price',  
            'highest_price', 
            'lowest_price',  
            'volume',        
            'time',          
            'created_at',    
            'created_ip',         
        ];
    }

    /**
     *
     * @return Array
    */
    public static function attrs()
    {
        return  [
                    'id',            
                    'coin_id',       
                    'coin_code',     
                    'price',         
                    'percentage',    
                    'diff',          
                    'open_price',    
                    'latest_price',  
                    'highest_price', 
                    'lowest_price',  
                    'volume',        
                    'time',          
                    'created_at',    
                    'created_ip',      
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
  

    public function getAll($ip,$type="rows"){

        $sql = $where_str = $select = '';
        $where_str_array = array();

        $select = '*';
        if (!empty($ip['select'])) {
            if (is_array($ip['select'])) {
                $select = implode(',', $ip['select']);
            } else {
                $select = $ip['select'];
            }
        }

        $where_str_array = [];
        if ( isset($ip['coin_code']) ){
            $where_str_array[] = " coin_code = '".$ip['coin_code']."' ";
        }

        $where_str = '1';
        if (!empty($where_str_array)) {
            $where_str = implode(' AND ', $where_str_array);
        }

        $page    = issetGet($ip,'page',1);
        $perPage = issetGet($ip,'perPage',10);

        $pageStart = ($page - 1) * $perPage;

        $limit = ' LIMIT '.$pageStart.','.$perPage;

        $orderby = ' ORDER BY time DESC ';

        $sql = 'SELECT '.$select.' FROM '.$this->tableName.' WHERE '.$where_str.' '.$orderby.' '.$limit.' ';
          
        $rows = $this->callsql($sql,$type);

        if(empty($rows)){
           $rows = [];
        }

        return $rows;

    }

    public function getAllForGraph($ip,$type="rows"){

        $sql = $where_str = $select = '';
        $where_str_array = array();

        $select = '*';
        if (!empty($ip['select'])) {
            if (is_array($ip['select'])) {
                $select = implode(',', $ip['select']);
            } else {
                $select = $ip['select'];
            }
        }

        $where_str_array = [];
        if ( isset($ip['coin_code']) ){
            $where_str_array[] = " coin_code = '".$ip['coin_code']."' ";
        }

        if (isset($ip['start_time']) && isset($ip['end_time'])) {
            $where_str_array[] = " time BETWEEN '".$ip['start_time']."' AND '".$ip['end_time']."' ";
        }

        $where_str = '1';
        if (!empty($where_str_array)) {
            $where_str = implode(' AND ', $where_str_array);
        }

        $orderby = ' ORDER BY time*1 ASC ';

        $sql = 'SELECT '.$select.' FROM '.$this->tableName.' WHERE '.$where_str.' '.$orderby.' ';
          
        $rows = $this->callsql($sql,$type);

        if(empty($rows)){
           $rows = [];
        }

        return $rows;

    }

    public function getTotalCount() {

        $totalCount = $this->callSql("SELECT COUNT(id) FROM $this->tableName ","value");

        return $totalCount;
    }

    public function getLast24HourPrice($coin_id){

        $time = time() - 86400;

        $price = $this->callSql("SELECT price FROM $this->tableName WHERE time < $time and coin_id = '$coin_id' ORDER BY time DESC LIMIT 1 ","value");

        return $price;
    }

}
