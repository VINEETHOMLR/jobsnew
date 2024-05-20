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
class CoinTrade extends Database {

    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */

    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "coin_trade";

        $this->columns = [
            'id',                  
            'user_id',             
            'coin_swap_from',      
            'coin_swap_to',        
            'swap_out_amout',      
            'swap_out_coin_price', 
            'swap_out_service_fee',
            'swap_in_amout',       
            'swap_in_coin_price',  
            'swap_in_service_fee', 
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
                    'coin_swap_from',      
                    'coin_swap_to',        
                    'swap_out_amout',      
                    'swap_out_coin_price', 
                    'swap_out_service_fee',
                    'swap_in_amout',       
                    'swap_in_coin_price',  
                    'swap_in_service_fee', 
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
        $coin_id = isset($ip['coin_id']) ? $ip['coin_id'] : 0;
        //$coin_code = isset($ip['coin_code']) ? $ip['coin_code'] : 0;
        $amount = isset($ip['amount']) ? $ip['amount'] : 0;
        $usd_value = isset($ip['usd_value']) ? $ip['usd_value'] : 0;
        $current_coin_price = isset($ip['current_coin_price']) ? $ip['current_coin_price'] : 0;
        $createtime = time();
        $ip = H::getIp();

        $query = "INSERT INTO $this->tableName (`user_id`,`coin_id`,`amount`,`usd_value`,`current_coin_price`,`swap_in_amout`,`swap_in_coin_price`,`swap_in_service_fee`,`executed_amount`,`exceuted_value`,`status`,`createtime`,`createip`) VALUES (:user_id,:coin_id,:amount,:usd_value,:current_coin_price,:swap_in_amount,:swap_in_coin_price,:swap_in_service_fee,:executed_amount,:executed_value,:status,:createtime,:createip)";

        $this->query($query);
        $this->bind(':user_id', $user_id);
        $this->bind(':coin_id', $coin_id);
        $this->bind(':amount', $amount);
        $this->bind(':usd_value', $usd_value);
        $this->bind(':current_coin_price', $current_coin_price);
        $this->bind(':createtime', $createtime);
        $this->bind(':createip', $ip);

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
            array_push($where_str_array,"  createtime >=  ".$start_from." ");  
        }

        if(!empty($start_to)) {
            //$start_to = strtotime($start_to.'23:59:59');
            array_push($where_str_array,"  createtime <=  ".$start_to." ");  
        }

        $where_str = '1';
        if (!empty($where_str_array)) {
            $where_str = implode(' AND ', $where_str_array);
        }

        $pageStart = ($page - 1) * $perPage;

        $limit = ' LIMIT '.$pageStart.','.$perPage;

        $getTotal = $this->callsql('SELECT count(id) FROM '.$this->tableName.' WHERE '.$where_str.' ','value');

        $sql = 'SELECT '.$select.' FROM '.$this->tableName.' WHERE '.$where_str.' ORDER BY createtime DESC '.$limit.' ';

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

    public function getHistoryData($ip,$filter){

        $sql = $where_str = $select = '';
        $where_str_array = array();

        $where_str_array[] = 'user_id=\''.$filter['player_id'].'\'';
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

            array_push($where_str_array,"  coin_swap_from =  '$coin_details' "); 
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
		
		$perPage1 = floor($perPage/2); 
		
		
        $pageStart = ($page - 1) * $perPage1;

        $limit = ' LIMIT '.$pageStart.','.$perPage1;



        $getTotal = $this->callsql('SELECT count(id) FROM '.$this->tableName.' WHERE '.$where_str.' ','value');


       $sql = 'SELECT '.$select.' FROM '.$this->tableName.' WHERE '.$where_str.'  ORDER BY created_at*1 DESC '.$limit.'  ';

       
        $rows = $this->callsql($sql,"rows");
		
		//$pagedata = count($rows);

        $resp = array();
        
        /*if (!empty($rows)) {
                foreach ($rows as $key => $info) {
                   
                    $resp[$key]['id'] = !empty($info['id'])?strval($info['id']):'-';
                    $resp[$key]['date'] = !empty($info['created_at'])?date('Y-m-d H:i:s',$info['created_at']):'-';
                    $resp[$key]['trans_type_text'] = 'Coinswap';//!empty($info['title'])?$info['title']:'-';
                    $resp[$key]['amount'] = !empty($info['swap_out_amout'])?strval($info['swap_out_amout']):'-';
                    $resp[$key]['trans_type'] = '3';
                }

        $totalPages = floor($getTotal/$perPage);
        if(($getTotal%$perPage)!=0){$totalPages = $totalPages+1;}    
        }*/
		$getTotal1 = $getTotal*2;
		if (!empty($rows)) {
		$key = 0;
			for($i=0;$i<count($rows);$i++)
			{
                $trans_type_text = Raise::t('common',$this->transactionArray['3']);
				$coin_to = "SELECT coin_code FROM coin WHERE id='".$rows[$i]['coin_swap_to']."' ";
				$coin_to = $this->callsql($coin_to,"value");
				
				$resp[$key]['id'] = !empty($rows[$i]['id'])?strval($rows[$i]['id']):'-';
				//echo $created_at = date('d M Y h:i:s A',$rows[$i]['created_at']);exit;
				$resp[$key]['date'] = !empty($rows[$i]['created_at'])?date('d M Y h:i:s A',$rows[$i]['created_at']):'-';
				$resp[$key]['trans_type_text'] = $trans_type_text;//!empty($info['title'])?$info['title']:'-';
				$resp[$key]['amount'] = !empty((-1)*$rows[$i]['swap_out_amout'])?strval("-".$rows[$i]['swap_out_amout']):'-';
				$resp[$key]['trans_type'] = '3';
				$resp[$key]['coin_code']  = !empty($coin_id)?strval($coin_id):'-';
				
				$resp[$key+1]['id'] = !empty($rows[$i]['id'])?strval($rows[$i]['id']):'-';
				$resp[$key+1]['date'] = !empty($rows[$i]['created_at'])?date('d M Y h:i:s A',$rows[$i]['created_at']):'-';
				$resp[$key+1]['trans_type_text'] = $trans_type_text;//!empty($info['title'])?$info['title']:'-';
				$resp[$key+1]['amount'] = !empty($rows[$i]['swap_in_amout'])?strval($rows[$i]['swap_in_amout']):'-';
				$resp[$key+1]['trans_type'] = '3';
				$resp[$key+1]['coin_code']  = !empty($coin_to)?strval($coin_to):'-';
				
				$key = $key+2;
			}
			
			$totalPages = $getTotal/$perPage;
        
			$totalPages = ceil($totalPages*2);
		
		}
		
			
		//$totalPages = ($getTotal1/$perPage1)-1;exit;
		$pagedata = count($resp);
		//print_r($resp);exit;
		
		/*else{
                    $resp[0]['id'] = '101';
                    $resp[0]['date'] = '2019-09-08 08:30:00';
                    $resp[0]['trans_type_text'] = 'Coinswap';//!empty($info['title'])?$info['title']:'-';
                    $resp[0]['amount'] = '23';
                    $resp[0]['trans_type'] = '3';
                    $resp[1]['id'] = '102';
                    $resp[1]['date'] = '2019-09-08 08:30:00';
                    $resp[1]['trans_type_text'] = 'Coinswap';//!empty($info['title'])?$info['title']:'-';
                    $resp[1]['amount'] = '21';
                    $resp[1]['trans_type'] = '3';
                    $totalPages = '1';
                    $getTotal = "2";
        }*/

        

        /*if(empty($rows)){
           $datarray['rows'] = [];
        }
        else{
            $datarray['rows'] = $rows;
        }*/
        $datarray['trans_history']['recordsTotal']      = !empty($getTotal*2)?strval($getTotal*2):'0';
        $datarray['trans_history']['recordsFiltered']   = !empty($pagedata)?strval($pagedata):'0';
        $datarray['trans_history']['totalPages']        = !empty($totalPages)?strval($totalPages):'0';
        $datarray['trans_history']['currentPage']       = !empty($page)?strval($page):'0';
        $datarray['trans_history']['recordsList']       = $resp;

        return $datarray;

    }


    public function getPendingOrderIds(){

        $one_min_before_time = time() - 60;
        $rows = $this->callsql("SELECT order_id FROM $this->tableName WHERE status = 0 and created_at < $one_min_before_time","rows");

        return $rows;

    }

    public function getOrderDeatails($order_id){
        $rows = $this->callsql("SELECT * FROM $this->tableName where order_id = '$order_id'","rows");
        return $rows;
    }

    public function insertCoinSwapData($data){


        $query = "INSERT INTO $this->tableName (`user_id`,`coin_swap_from`,`coin_swap_to`,`swap_out_amout`,`swap_out_coin_price`,`order_id`,`side`,`swap_in_amout`,`swap_in_coin_price`,`swap_in_service_fee`,`executed_amount`,`exceuted_value`,`status`,`created_at`,`created_ip`) VALUES (:user_id,:coin_swap_from,:coin_swap_to,:swap_out_amout,:swap_out_coin_price,:order_id,:side,:swap_in_amount,:swap_in_coin_price,:swap_in_service_fee,:executed_amount,:executed_value,:status,:created_at,:created_ip)";

        $this->query($query);
        $this->bind(':user_id', $data['user_id']);
        $this->bind(':coin_swap_from', $data['coin_swap_from']);
        $this->bind(':coin_swap_to', $data['coin_swap_to']);
        $this->bind(':swap_out_amout', $data['swap_out_amout']);
        $this->bind(':swap_out_coin_price', $data['swap_out_coin_price']);
        $this->bind(':side', $data['side']);
        $this->bind(':order_id', $data['order_id']);
        $this->bind(':swap_in_amount', $data['swap_in_amount']);
        $this->bind(':swap_in_coin_price', $data['swap_in_coin_price']);
        $this->bind(':swap_in_service_fee', $data['fee']);
        $this->bind(':executed_amount', $data['executed_amount']);
        $this->bind(':executed_value', $data['executed_value']);
        $this->bind(':status', $data['status']);
        $this->bind(':created_at', $data['created_at']);
        $this->bind(':created_ip', $data['created_ip']);
        
        
        $this->execute();
        return $this->lastInsertId();
    }

    public function updateTarde($order_id,$data){


        $side              = $data['side'];
        $price             = $data['price'];          
        $executed_amount   = $data['executed_amount'];
        $executed_value    = $data['executed_value'];
        $fee               = $data['fee'];
        $status            = 1;
        $updated_at        = time();


        if($side == 1) // sell
        {
            $swap_in_amount = $executed_value;

        }else if ($side == 2){ //buy
            $swap_in_amount = $executed_amount;
        }

        $query = "UPDATE $this->tableName SET 
                                swap_in_amout = :swap_in_amount,
                                swap_in_coin_price = :swap_in_coin_price,
                                swap_in_service_fee = :swap_in_service_fee,
                                executed_amount = :executed_amount,
                                exceuted_value = :executed_value,
                                status = :status,
                                updated_at = :updated_at  
                                WHERE order_id = :order_id";



        $this->query($query);

        $this->bind(':swap_in_amount', $swap_in_amount);
        $this->bind(':swap_in_coin_price', $price);
        $this->bind(':swap_in_service_fee', $fee);
        $this->bind(':executed_amount', $executed_amount);
        $this->bind(':executed_value', $executed_value);
        $this->bind(':status', $status);
        $this->bind(':updated_at', $updated_at);
        $this->bind(':order_id', $order_id);
        

        return $this->execute();

    }


}
