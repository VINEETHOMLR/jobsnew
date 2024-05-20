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
class UserWallet extends Database {

    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */

    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "user_wallet";

        $this->columns = [
            'id',                  
            'user_id',             
            'btc_wallet',      
            'eth_wallet',        
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
     * @return Array
    */
    public static function attrs()
    {
        return  [
                    'id',                  
                    'user_id',             
                    'btc_wallet',      
                    'eth_wallet',        
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

    public function insert($ip) {

        $user_id = isset($ip['user_id']) ? $ip['user_id'] : 0;
    
        
        $btc_wallet = isset($ip['btc_wallet']) ? $ip['btc_wallet'] : 0;
        $eth_wallet = isset($ip['eth_wallet']) ? $ip['eth_wallet'] : 0;
      
        $created_at = time();
        $created_ip = H::getIp();
        $created_by = isset($ip['created_by']) ? $ip['created_by'] : 0;

        $query = "INSERT INTO $this->tableName (`user_id`,`btc_wallet`,`eth_wallet`,`created_at`,`created_by`,`created_ip`) VALUES (:user_id,:btc_wallet,:eth_wallet,:created_at,:created_by,:created_ip)";

        $this->query($query);
        $this->bind(':user_id', $user_id);
        $this->bind(':btc_wallet', $btc_wallet);
        $this->bind(':eth_wallet', $eth_wallet);
        $this->bind(':created_at', $created_at);
        $this->bind(':created_by', $created_by);
        $this->bind(':created_ip', $created_ip);
       

        $this->execute();

        return true;
    }

    public function getAll($user_id,$filter=[]){

        $sql = $where_str = $select = '';
        $where_str_array = array();

        $where_str_array[] = 'user_id=\''.$user_id.'\'';

        $select = '*';
        if (!empty($filter['select'])) {
            if (is_array($filter['select'])) {
                $select = implode(',', $filter['select']);
            } else {
                $select = $filter['select'];
            }
        }

        if(!empty($filter['page'])){
            $page = !empty($filter['page'])?$filter['page']:'1';
            $perPage = !empty($filter['perPage'])?$filter['perPage']:'10';
        }

        $start_from = !empty($filter['start_from'])?$filter['start_from']:'';
        $start_to = !empty($filter['start_to'])?$filter['start_to']:'';
       // $coin_id = !empty($filter['coin_id'])?$filter['coin_id']:'';
        
        if(!empty($start_from)) {
            //$start_from = strtotime($start_from.'00:00:00');
            array_push($where_str_array,"  created_at >=  ".$start_from." ");  
        }

        if(!empty($start_to)) {
            //$start_to = strtotime($start_to.'23:59:59');
            array_push($where_str_array,"  created_at <=  ".$start_to." ");  
        }

        $where_str = '1';
        if (!empty($where_str_array)) {
            $where_str = implode(' AND ', $where_str_array);
        }

        $pageStart = ($page - 1) * $perPage;

        $limit = ' LIMIT '.$pageStart.','.$perPage;

        $getTotal = $this->callsql('SELECT count(id) FROM '.$this->tableName.' WHERE '.$where_str.' ','value');

        $sql = 'SELECT '.$select.' FROM '.$this->tableName.' WHERE '.$where_str.' ORDER BY created_at DESC '.$limit.' ';

        $rows = $this->callsql($sql,"rows");

        if(empty($rows)){
           $datarray['rows'] = [];
        }
        else{
            $datarray['rows'] = $rows;
        }

        $datarray['totalCount'] = $getTotal;

        return $datarray;

    }

    public function getAllBalance($ip,$filter){

        $sql = $where_str = $select = '';
        $where_str_array = array();

        $where_str_array[] = 'user_id=\''.$filter['player_id'].'\'';
        if(!empty($filter['page'])){
            $page = !empty($filter['page'])?$filter['page']:'1';
            $perPage = !empty($filter['perPage'])?$filter['perPage']:'10';
        }

        //$start_from = !empty($filter['start_from'])?$filter['start_from']:'';
        //$start_to = !empty($filter['start_to'])?$filter['start_to']:'';
        

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
       
        //$pageStart = ($page - 1) * $perPage;

        $limit = '';


        $sql = 'SELECT '.$select.' FROM '.$this->tableName.' WHERE '.$where_str.'  ORDER BY created_at*1 DESC '.$limit.'  ';

        $sql2 = 'SELECT coin_name,coin_code,value FROM coin WHERE status=1 ';

       
        $rows = $this->callsql($sql,"rows");

       
        

        $rows2 = $this->callsql($sql2,"rows");

        $resp = array();

        $total_balance = 0;

        if (!empty($rows2)) {
                
                foreach ($rows2 as $key => $resp) {
                    
                   
                    $resu[$key]['coin_code']  =$coin_code= !empty($resp['coin_code'])?strval($resp['coin_code']):'-';
                    $resu[$key]['coin_name']  = !empty($resp['coin_name'])?strval($resp['coin_name']):'-';
                    $resu[$key]['balance']    = !empty($rows[0][''.$coin_code.'_wallet'])?strval($rows[0][''.$coin_code.'_wallet']):'0';
                    $resu[$key]['coin_value'] = !empty($resp['value'])?strval($resp['value']):'0';

                    $total_balance            = $total_balance + $rows[0][''.$coin_code.'_wallet'];
                   
                }

        }

        
        $datarray['total_balance'] = strval($total_balance);
        $datarray['available_balance'] = strval($total_balance);
        $datarray['frozen_balance'] = "0";
        $datarray['coin_wallets'] = $resu ; 

        //print_r($datarray);exit;

        return $datarray;

    }

    public function getAllWalletTransaction($filter){

        $sql = $where_str = $select = '';
        $where_str_array = array();

        $where_str_array[] = 'user_id=\''.$filter['player_id'].'\'';
        if(!empty($filter['page'])){
            $page = !empty($filter['page'])?$filter['page']:'1';
            $perPage = !empty($filter['perPage'])?$filter['perPage']:'10';
        }
  
     
        $coin_id = !empty($filter['coin_code'])?$filter['coin_code']:'';

        $coin = "SELECT id,coin_name,value FROM coin WHERE coin_code='$coin_id' ";
        $coin_details = $this->callsql($coin,"rows");
        
        //if($coin_id != "") {
        //    array_push($where_str_array,"  coin_id =  '$coin_id' ");  
            // $start_to = strtotime($start_to.'23:59:59');
        //}
        
        $where_str = '1';
        if (!empty($where_str_array)) {
            $where_str = implode(' AND ', $where_str_array);
        }

        
        $pageStart = ($page - 1) * $perPage;

        $limit = ' LIMIT '.$pageStart.','.$perPage;

        $sql = 'SELECT '.$coin_id.'_wallet FROM '.$this->tableName.' WHERE '.$where_str.'  ';

        $total_balance = $this->callsql($sql,"value");

        $getTotal1 = $this->callsql('SELECT count(id) FROM deposit WHERE '.$where_str.' ','value');
        $getTotal2 = $this->callsql('SELECT count(id) FROM coin_withdrawal WHERE '.$where_str.' ','value');
        $getTotal3 = $this->callsql('SELECT count(id) FROM coin_trade WHERE '.$where_str.' ','value');

        $getTotal = $getTotal1 + $getTotal2 + $getTotal3;
        //$sql1 = 'SELECT wd.amount,wd.created_at,dp.amount,dp.created_at,ct.swap_out_amout,ct.created_at FROM deposit AS dp LEFT JOIN coin_withdrawal AS wd ON dp.user_id=wd.user_id LEFT JOIN coin_trade AS ct ON dp.user_id=ct.user_id WHERE dp.user_id=\''.$filter['player_id'].'\' AND (dp.coin_id=\''.$coin_details[0]['id'].'\' OR wd.coin_id=\''.$coin_details[0]['id'].'\' OR ct.coin_swap_from=\''.$coin_details[0]['id'].'\' ) ORDER BY dp.created_at*1 DESC  '.$limit.'  ';
        $sql = '(SELECT id,amount as amt,created_at , 1 as diff FROM deposit WHERE user_id=\''.$filter['player_id'].'\' AND coin_id=\''.$coin_details[0]['id'].'\' ORDER BY created_at*1 DESC ) UNION (SELECT id,amount as amt,created_at, 2 as diff FROM coin_withdrawal WHERE user_id=\''.$filter['player_id'].'\' AND coin_id=\''.$coin_details[0]['id'].'\' ORDER BY created_at*1 DESC ) UNION ( SELECT id,swap_out_amout as amt,created_at, 3 as diff  FROM coin_trade WHERE user_id=\''.$filter['player_id'].'\' AND coin_swap_from=\''.$coin_details[0]['id'].'\' ORDER BY created_at*1 DESC ) '.$limit.'   ';

        $rows = $this->callsql($sql,"rows");

       // print_r($rows);exit;

        //if($rows1 < $perPage){

        //    $sql2 = 'SELECT id,amount,created_at FROM coin_withdrawal WHERE user_id=\''.$filter['player_id'].'\' AND coin_id=\''.$coin_details[0]['id'].'\' ORDER BY created_at*1 DESC '.$limit.'  ';

        //    $rows2 = $this->callsql($sql2,"rows");

        ///}

        

        //$sql3 = 'SELECT id,swap_out_amout,created_at FROM coin_trade WHERE user_id=\''.$filter['player_id'].'\' AND coin_swap_from=\''.$coin_details[0]['id'].'\' ORDER BY created_at*1 DESC '.$limit.'  ';

        //$rows3 = $this->callsql($sql3,"rows");

       
        
        
        


        $resp = array();

        $numb = 0;

        if (!empty($rows)) {
                foreach ($rows as $key => $info) {

                    if($info['diff']==1){
                        $trans_type_text = 'Deposit';
                    }elseif ($info['diff']==2) {
                        $trans_type_text = 'Withdrawal';
                    }else{
                        $trans_type_text = 'coin swap';
                    }
                   
                    $resp[$key]['id'] = !empty($info['id'])?strval($info['id']):'-';
                    $resp[$key]['date'] = !empty($info['created_at'])?date('Y-m-d H:i:s',$info['created_at']):'-';
                    $resp[$key]['trans_type_text'] = $trans_type_text;//!empty($info['title'])?$info['title']:'-';
                    $resp[$key]['amount'] = !empty($info['amt'])?strval($info['amt']):'-';
                    $resp[$key]['trans_type'] = !empty($info['diff'])?strval($info['diff']):'-';
                }

        $totalPages = floor($getTotal/$perPage);
        if(($getTotal%$perPage)!=0){$totalPages = $totalPages+1;} 
        }



        /*if (!empty($rows2)) {
                foreach ($rows2 as $key => $info) {
                    $key1 = $numb+$key; 
                    $resp[$key1]['id'] = !empty($info['id'])?strval($info['id']):'-';
                    $resp[$key1]['date'] = !empty($info['created_at'])?date('Y-m-d H:i:s',$info['created_at']):'-';
                    $resp[$key1]['trans_type_text'] = 'Withdrwal';//!empty($info['title'])?$info['title']:'-';
                    $resp[$key1]['amount'] = !empty($info['amount'])?strval($info['amount']):'-';
                    $resp[$key1]['trans_type'] = '2';
                }

            $numb =$key1 +1 ;
        }

        if (!empty($rows3)) {
                foreach ($rows3 as $key => $info) {
                    $key1 = $numb+$key;
                    $resp[$key1]['id'] = !empty($info['id'])?strval($info['id']):'-';
                    $resp[$key1]['date'] = !empty($info['created_at'])?date('Y-m-d H:i:s',$info['created_at']):'-';
                    $resp[$key1]['trans_type_text'] = 'Coinswap';//!empty($info['title'])?$info['title']:'-';
                    $resp[$key1]['amount'] = !empty($info['swap_out_amout'])?strval($info['swap_out_amout']):'-';
                    $resp[$key1]['trans_type'] = '3';
                }
  
        }*/

        

        

        /*if(empty($rows)){
           $datarray['rows'] = [];
        }
        else{
            $datarray['rows'] = $rows;
        }*/
        $datarray['coin_code']                          = !empty($coin_id)?strval($coin_id):'0';
        $datarray['coin_name']                          = !empty($coin_details[0]['coin_name'])?strval($coin_details[0]['coin_name']):'-';
        $datarray['total_balance']                      = !empty($total_balance)?strval($total_balance):'0';
        $datarray['available_balance']                  = !empty($total_balance)?strval($total_balance):'0';
        $datarray['frozen_balance']                     = "0";

        $datarray['trans_history']['recordsTotal']      = !empty($getTotal)?strval($getTotal):'0';
        $datarray['trans_history']['recordsFiltered']   = !empty($perPage)?strval($perPage):'0';
        $datarray['trans_history']['totalPages']        = !empty($totalPages)?strval($totalPages):'0';
        $datarray['trans_history']['currentPage']       = !empty($getTotal)?strval($page):'0';
        $datarray['trans_history']['recordsList']       = $resp;

//print_r($datarray);exit;
        return $datarray;

    }


}
