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
class Deposit extends Database {

    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */

    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "deposit";

        $this->columns = [
            'id',                
            'user_id',           
            'amount',            
            'from_address',      
            'to_address',        
            'coin_id',           
            'current_coin_price',
            'service_charge',    
            'trans_hash',        
            'status',            
            'created_at',        
            'created_by',        
            'created_ip',        
            'updated_at',        
            'updated_by',        
            'updated_ip',        
        ];
        global $transactionArray;
        $this->transactionArray = $transactionArray;
    }

    /**
     *
     * @return Array
    */
    public static function attrs()
    {
        return  [
                    'id',                
                    'user_id',           
                    'amount',            
                    'from_address',      
                    'to_address',        
                    'coin_id',           
                    'current_coin_price',
                    'service_charge',    
                    'trans_hash',        
                    'status',            
                    'created_at',        
                    'created_by',        
                    'created_ip',        
                    'updated_at',        
                    'updated_by',        
                    'updated_ip',   
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
  

    public function getAll($user_id,$ip){

        $sql = $where_str = $select = '';
        $where_str_array = array();

        $where_str_array[] = 'user_id=\''.$user_id.'\'';

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


    public function getHistoryData($ip,$filter){

      
        $sql = $where_str = $select = '';
        $where_str_array = array();

        $where_str_array[] = 'user_id=\''.$filter['player_id'].'\'';
		
		$where_str_array[] = ' status=2 ';
		
        if(!empty($filter['page'])){
            $page = !empty($filter['page'])?$filter['page']:'1';
            $perPage = !empty($filter['perPage'])?$filter['perPage']:'10';
        }

        //$start_from = !empty($filter['start_from'])?$filter['start_from']:'';
        //$start_to = !empty($filter['start_to'])?$filter['start_to']:'';
        $coin_id = !empty($filter['coin_code'])?$filter['coin_code']:'';

        /*
         if(!empty($start_from)) {
            //$start_from = strtotime($start_from.'00:00:00');
            array_push($where_str_array,"  createtime >=  ".$start_from." ");  
        }
        if(!empty($start_to)) {
            //$start_to = strtotime($start_to.'23:59:59');
            array_push($where_str_array,"  createtime <=  ".$start_to." ");  
        }
        */
        if($coin_id != "") {

            $coin = "SELECT id FROM coin WHERE coin_code='$coin_id' ";
            $coin_details = $this->callsql($coin,"value");

            array_push($where_str_array,"  coin_id =  '$coin_details' ");  
            // $start_to = strtotime($start_to.'23:59:59');
        }
        
        $where_str = '1';
        if (!empty($where_str_array)) {
            $where_str = implode(' AND ', $where_str_array);
        }

        $select = '*';
        if (!empty($ip)) {
            if (is_array($ip)) {
                $select = implode(',', $ip);
            } else {
                $select = $ip;
            }
        }
       
        $pageStart = ($page - 1) * $perPage;

        $limit = ' LIMIT '.$pageStart.','.$perPage;



        $getTotal = $this->callsql('SELECT count(id) FROM '.$this->tableName.' WHERE '.$where_str.' ','value');


       $sql = 'SELECT '.$select.' FROM '.$this->tableName.' WHERE '.$where_str.'  ORDER BY created_at*1 DESC '.$limit.'  ';

       
        $rows = $this->callsql($sql,"rows");

        $resp = array();
		
		$pagedata = count($rows);
        
        if (!empty($rows)) {
                foreach ($rows as $key => $info) {

                    //echo $this->transactionArray['1'];exit;
                    $trans_type_text = Raise::t('common',$this->transactionArray['1']);
                   
                    $resp[$key]['id'] = !empty($info['id'])?strval($info['id']):'-';
                    $resp[$key]['date'] = !empty($info['created_at'])?date('d M Y h:i:s A',$info['created_at']):'-';
                    $resp[$key]['trans_type_text'] = $trans_type_text;//!empty($info['title'])?$info['title']:'-';
                    $resp[$key]['amount'] = !empty($info['amount'])?strval($info['amount']):'-';
                    $resp[$key]['trans_type'] = '1';
                }

        $totalPages = floor($getTotal/$perPage);
        if(($getTotal%$perPage)!=0){$totalPages = $totalPages+1;}    
        }/*else{
                    $resp[0]['id'] = '101';
                    $resp[0]['date'] = '2019-09-08 08:30:00';
                    $resp[0]['trans_type_text'] = 'Deposit';//!empty($info['title'])?$info['title']:'-';
                    $resp[0]['amount'] = '23';
                    $resp[0]['trans_type'] = '1';
                    $resp[1]['id'] = '102';
                    $resp[1]['date'] = '2019-09-08 08:30:00';
                    $resp[1]['trans_type_text'] = 'Deposit';//!empty($info['title'])?$info['title']:'-';
                    $resp[1]['amount'] = '21';
                    $resp[1]['trans_type'] = '1';
                    $totalPages = '1';
                    $getTotal = "2";
        }*/

        

        /*if(empty($rows)){
           $datarray['rows'] = [];
        }
        else{
            $datarray['rows'] = $rows;
        }*/
        $datarray['trans_history']['recordsTotal']      = !empty($getTotal)?strval($getTotal):'0';
        $datarray['trans_history']['recordsFiltered']   = !empty($pagedata)?strval($pagedata):'0';
        $datarray['trans_history']['totalPages']        = !empty($totalPages)?strval($totalPages):'0';
        $datarray['trans_history']['currentPage']       = !empty($getTotal)?strval($page):'0';
        $datarray['trans_history']['recordsList']       = $resp;

        return $datarray;

    }

    public function checkduplicate($txid, $coinId, $receiver)
    {
        $check = $this->callSql("SELECT id FROM $this->tableName WHERE trans_hash='$txid' AND coin_id='$coinId' AND to_address='$receiver' ", 'value');

        return $check;
    }


    public function checkAndInsertTransaction($trans, $coinId, $userId)
    {
        $category = $trans['category'];
        $amount   = $trans['amount'];
        $confirmations = $trans['confirmations'];
        $receiver = $trans['address'];
        $transtime     = $trans['time'];

        $txid     = $trans['txid'];

        $time = time();
        $ip = $_SERVER['REMOTE_ADDR'];

        $this->callSql("INSERT INTO $this->tableName SET user_id='$userId',amount='$amount',to_address='$receiver',
                                coin_id='$coinId',trans_hash='$txid',created_at='$transtime',status=0,created_ip='$ip' ");
    }

    public function getPendingTransactions($coinId)
    {
        $transactions = $this->callSql("SELECT trans_hash FROM $this->tableName WHERE status=0 AND coin_id=$coinId GROUP BY trans_hash ", 'rows');

        return $transactions;
    }


    public function checkPendingOrNot($txid, $coinId, $receiver)
    {
        $check = $this->callSql("SELECT id FROM $this->tableName WHERE  trans_hash='$txid' AND coin_id='$coinId' AND to_address='$receiver' AND status=0 ", 'value');

        return $check;
    }

    public function updateTransactionStatus($id)
    {
        $time = time();
        $ip = $_SERVER['REMOTE_ADDR'];

        $this->callSql("UPDATE $this->tableName SET status=2 ,updated_at ='$time',updated_ip='$ip' WHERE id='$id'");
    }

}
