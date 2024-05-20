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
class CoinWithdrawal extends Database {

    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */

    public function __construct($db = "db")
    {
        
        parent::__construct(Raise::db()[$db]);

        $this->tableName = "coin_withdrawal";

        $this->columns = [
            'id',           
            'user_id',      
            'coin_id',      
            'coin_code',    
            'amount',       
            'service_charge',
            'to_address',   
            'trans_hash',   
            'status',       
            'remarks',      
            'created_at',   
            'created_by',   
            'created_ip',   
            'updated_time', 
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
                    'coin_id',      
                    'coin_code',    
                    'amount',       
                    'service_charge',
                    'to_address',   
                    'trans_hash',   
                    'status',       
                    'remarks',      
                    'created_at',   
                    'created_by',   
                    'created_ip',   
                    'updated_time', 
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
        $coin_code = isset($ip['coin_code']) ? $ip['coin_code'] : 0;
        $service_charge = isset($ip['service_charge']) ? $ip['service_charge'] : 0;
        $amount = isset($ip['amount']) ? $ip['amount'] : 0;
        $to_address = isset($ip['to_address']) ? $ip['to_address'] : "";
        $status = isset($ip['status']) ? $ip['status'] : 0;
        $transhash = isset($ip['transhash']) ? $ip['transhash'] : "";
        $auto_withdrawal = isset($ip['auto_withdrawal']) ? $ip['auto_withdrawal'] : 0;
        $processing_status = isset($ip['processing_status']) ? $ip['processing_status'] : 0;
        $createtime = time();
        $ip = H::getIp();

        $query = "INSERT INTO $this->tableName (`user_id`,`coin_id`,`coin_code`,`service_charge`,`amount`,`createtime`,`createip`,`to_address`,`status`,`transhash`,`auto_withdrawal`,`processing_status`) VALUES (:user_id,:coin_id,:coin_code,:service_charge,:amount,:createtime,:createip,:to_address,:status,:transhash,:auto_withdrawal,:processing_status)";

        $this->query($query);
        $this->bind(':user_id', $user_id);
        $this->bind(':coin_id', $coin_id);
        $this->bind(':coin_code', $coin_code);
        $this->bind(':service_charge', $service_charge);
        $this->bind(':amount', $amount);
        $this->bind(':createtime', $createtime);
        $this->bind(':createip', $ip);
        $this->bind(':to_address', $to_address);
        $this->bind(':status', $status);
        $this->bind(':transhash', $transhash);
        $this->bind(':auto_withdrawal', $auto_withdrawal);
        $this->bind(':processing_status', $processing_status);

        $this->execute();

        return true;
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

          
        $rows = $this->callsql("SELECT * FROM $this->tableName WHERE user_id = '$user_id' ","rows");

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

                    $trans_type_text = Raise::t('common',$this->transactionArray['2']);
                    $resp[$key]['id'] = !empty($info['id'])?strval($info['id']):'-';
                    $resp[$key]['date'] = !empty($info['created_at'])?date('d M Y h:i:s A',$info['created_at']):'-';
                    $resp[$key]['trans_type_text'] = $trans_type_text;//!empty($info['title'])?$info['title']:'-';
                    $resp[$key]['amount'] = !empty($info['amount'])?strval($info['amount']):'-';
                    $resp[$key]['trans_type'] = '2';
                }

        $totalPages = floor($getTotal/$perPage);
        if(($getTotal%$perPage)!=0){$totalPages = $totalPages+1;}    
        }/*else{
                    $resp[0]['id'] = '101';
                    $resp[0]['date'] = '2019-09-08 08:30:00';
                    $resp[0]['trans_type_text'] = 'Withdrawal';//!empty($info['title'])?$info['title']:'-';
                    $resp[0]['amount'] = '23';
                    $resp[0]['trans_type'] = '1';
                    $resp[1]['id'] = '102';
                    $resp[1]['date'] = '2019-09-08 08:30:00';
                    $resp[1]['trans_type_text'] = 'Withdrawal';//!empty($info['title'])?$info['title']:'-';
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

    public function getPendingCoinAutoWithdrawals($limit = 100){

        $sql = 'SELECT * FROM '.$this->tableName.' WHERE auto_withdrawal = 1 AND processing_status = 0 AND status = 1 ORDER BY id ASC LIMIT '.$limit.' ';

        $rows = $this->callsql($sql,"rows");

        if(empty($rows)){
           $rows = [];
        }

        return $rows;

    }

    public function updateProcessingStatus($ids = []) {

        if (empty($ids)) {
            return false;
        }

        $implode = implode(",",$ids);

        $this->query("UPDATE $this->tableName SET processing_status = '1' WHERE id IN($implode) AND processing_status = 0 AND auto_withdrawal = 1 AND status = 1 ");
        $this->execute();

        return true;
    }

    public function processAutoWithdrawal() {

        $limit = 100;

        $rows = $this->getPendingCoinAutoWithdrawals($limit); 

        if (empty($rows)) { 
           return ['status'=>true,"message"=>"No withdrawals Pending to process"];
        }

        $ids = array_column($rows, "id");

        $this->updateProcessingStatus($ids);

        $masteraddress = (new SiteData)->getSiteData("masteraddress");

        foreach($rows as $row) {
            
            $data['id'] = $row['id'];
            $data['masteraddress'] = $masteraddress;

            $this->singleApproveCoin($data);
        }

        return ['status'=>true,"message"=>"Autowithdrawal cron run Successfully"];
    }

    public function singleApproveCoin($data)
    {
        global $config_wallet_decimal_limits;

        $id = $data['id'];
        $masteraddress = $data['masteraddress'];
        
        $detail = $this->callsql("SELECT user_id,coin_id,coin_code,to_address,amount,service_charge FROM $this->tableName WHERE id='$id' AND status = 1 AND auto_withdrawal = 1 AND processing_status = 1 ", 'row');

        if (empty($detail)) {
            return false;
        }

        $time = time();
        $user_id = $detail['user_id'];
        $coin_id = $detail['coin_id'];
        $coin_code = $detail['coin_code'];
        $toAddress = $detail['to_address'];
        $fromAddress = $masteraddress;

        $decimal_limit = isset($config_wallet_decimal_limits[$coin_code]) ? $config_wallet_decimal_limits[$coin_code] : 2;

        $amount = bcsub($detail['amount'] , $detail['service_charge'],$decimal_limit);

        $response = (new CoinClass)->sendCoin($user_id,$coin_code,$amount,$toAddress,$fromAddress,$id);

        if ($response['status'] == "error") {

            $message = $response['message'];
            $remarks = "Autowithdrawal Process Failed";

            $this->query("UPDATE $this->tableName SET response = '$message',`updatetime`='$time',remarks = '$remarks',processing_status = '0' WHERE `id`='$id' ");

            $this->execute();

            return false;

        } else {

            $transhash = $response['message'];

            $remarks = "Withdrawal Successfully Processed";

            $this->query("UPDATE $this->tableName SET `transhash`='$transhash', `status`='2', remarks='$remarks', `updateid`='0', `updatetime`='$time', `updateip`='',processing_status = 2  WHERE `id`='$id' ");
            $this->execute();

            return true;
        }
    }


    public function insertCoinWalletRequest($ip){

        $query = "INSERT INTO $this->tableName (`user_id`,`coin_id`,`coin_code`,`amount`,`service_charge`,`to_address`,`trans_hash`,`status`,`remarks`,`created_at`,`created_by`,`created_ip`) VALUES (:user_id,:coin_id,:coin_code,:amount,:service_charge,:to_address,:trans_hash,:status,:remarks,:created_at,:created_by,:created_ip)";
            $this->query($query);
            $this->bind(':user_id', $ip['user_id']);
            $this->bind(':coin_id', $ip['coin_id']);
            $this->bind(':coin_code',$ip['coin_code']);
            $this->bind(':amount', $ip['amount']);
            $this->bind(':service_charge', $ip['service_charge']);
            $this->bind(':to_address', $ip['to_address']);
            $this->bind(':trans_hash', $ip['trans_hash']);
            $this->bind(':status', $ip['status']);
            $this->bind(':remarks', $ip['remarks']);
            $this->bind(':created_at', $ip['created_at']);
            $this->bind(':created_by', $ip['created_by']);
            $this->bind(':created_ip', getClientIP());

            $this->execute();

    }

}
