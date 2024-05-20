<?php

/*
Error list
0) Success
1) API Key is not exist
2) IP is not whitelisted
3) Client blocked
4) Invalid parameter
5）Value cannot empty
6) Encrpytion verify is failed
7) Mode is not exist

Login API
101) Username or password wrong
102) Login already exist

Deposit API
201) User is not exist
202) Currency is not allow
203) Amount must be in number
204) Amount must be greater than 0
205) Transaction exist, second submit is not allow
206) Login record not found
207) Coin not found

Check Transaction API (Wallet APP)
301) Transaction not found
 
 */

namespace src\lib;

include_once 'walletClass.php';
use src\lib\CoinClass;
use src\lib\MarketPriceClass;

class ApiClass extends walletClass{
    //put your code here
    
    public function __construct() {
        parent::__construct();
        $this->apiKey = "testapi1";
        $this->secretKey = "c40b0c360f3d4959b53b103b25759542";
		//$this->secretKey = "b0295a8427eebf9c0f835108ee08cb4a";
        $this->lastLog = "";
        $this->apiMode = array("login","deposit");
        $this->currencyAllow = array("BTC","ETH","USDT");

        // Wallet APP Key
        $this->app_key = "b0295a8427eebf9c0f835108ee08cb4a";

        //$this->cObj  = (new commonClass);
    }
    
    public function LoginApi($data)
    {

        $username 		= $data['username'];
        $password 		= $data['password'];
		$this->userid	= $data['user_id'];
        $url = "/login";

        $data = array('api_key'=> $this->apiKey,'mode'=> 'login' ,'password'=> $password,'username'=> $username);
		$data = http_build_query($data);
//echo $data;exit;
        $encrypt = $this->Encrypt($data,$this->secretKey);
		
        $array = array('api_key'=> $this->apiKey,'mode'=> 'login' ,'password'=> $password ,'username'=> $username,'encryptKey'=> $encrypt);
        $result = $this->callCurl($array,$url);
        $dbResult = $result;
        $result = json_decode($result,true);
		
		$res = [];
	
        if($result['status'] == '0')
        {
            $res['status'] = 'success';
			$res['code']   = '0'; 
			$res['msg']	   = $result['message'];
            //$this->db->callsql("UPDATE coin SET coin_value='$price' WHERE coin_id='$coindb[coin_id]'");
			
            
        }
        else
		{
			$res['status']	='failed';
			$res['code']	=$result['status'];
			$res['msg']	   = $result['message'];
		}
		return $res;
            //return 
    }

	public function DepositeApi($data)
    {
	
		//$time			= time();
        $username 		= $data['username'];
        $amount 		= $data['amount'];
		$currency 		= $data['coin_code'];
		$trans_id		= $data['trans_id'];
		$this->userid	= $data['user_id'];
        
        
		$url = "/deposit";
		//print_r($data);exit;
		
		//$this->query("INSERT INTO finance_trade SET user_id='$data[user_id]',coin_code='$currency',amount='$amount',pass_code='$data[pass_code]',status=0");
		//$this->execute();
		
		
		$trans_id1 = "INM".$trans_id;

        $data = array('accountId'=> $username,'amount'=> $amount ,'api_key'=> $this->apiKey,'currency'=> $currency,'mode'=> 'deposit','trans_id'=> $trans_id1);
		$data = http_build_query($data);
//echo $data;exit;
        $encrypt = $this->Encrypt($data,$this->secretKey);
		
        $array = array('accountId'=> $username,'amount'=> $amount ,'api_key'=> $this->apiKey,'currency'=> $currency,'mode'=> 'deposit','trans_id'=> $trans_id1,'encryptKey'=> $encrypt);
		
		//print_r($array);exit;
		
        $result = $this->callCurl($array,$url);
        $dbResult = $result;
        $result = json_decode($result,true);
		//print_r($result);exit;
		$res = [];
	
        if($result['status'] == '0')
        {
            $res['status'] = 'success';
			$res['code']   = '0'; 
			$res['msg']	   = $result['message'];
			//$this->query("UPDATE  finance_trade SET status=1 WHERE id='$trans_id' AND user_id='$data[user_id]' ");
			//$this->execute();
            //$this->db->callsql("UPDATE coin SET coin_value='$price' WHERE coin_id='$coindb[coin_id]'");
			
            
        }
        else
		{
			$res['status']	='failed';
			$res['code']	=$result['status'];
			$res['msg']	   = $result['message'];
		}
		return $res;
            //return 
    }
	
	public function confirmDeposit($data)
    {
	    $params = [];
        $params['api_request_param'] = $data;
        $log_id = $this->addapilog2([],'confirm_Deposit',$params);

        $decrypt = $this->Decrypt($data,$this->app_key);
        if(empty($decrypt)) {
            
            $res['status'] = 'failed';
            $res['code']   = '400'; 
            $res['msg']    = "Decryption failed";
            $params = [];
            $params['api_response'] = json_encode($res);
            $this->updateapilog2([],$log_id,$params);
            return $res;
        }
        

        $params = [];
        $params['raw_request'] = json_encode($decrypt);
        $this->updateapilog2([],$log_id,$params);
		
		//$this->addapilog($decrypt,"confirm_Deposit");
       
		$datas = explode("#",$decrypt);
	
		$transId = str_replace('INM', '', $datas[0]);
		
		$result = $this->callSql("SELECT id FROM finance_trade WHERE id='$transId' ",'row');
		
 
		$res = [];
	
        if(!empty($result))
        {
			if($result['id']!="")
			{
				$res['status'] = 'success';
				$res['code']   = '200'; 
				$res['msg']	   = 'deposit confirmed';
			}
			else
			{
				$res['status'] = 'failed';
				$res['code']   = '400'; 
				$res['msg']	   = "transaction not found";
			}

        }
        else
		{
			$res['status']	='failed';
			$res['code']	="400";
			$res['msg']	   = "transaction not found";
		}
		
		//$this->updateapilog(json_encode($res));
        $params = [];
        $params['api_response'] = json_encode($res);
        $this->updateapilog2([],$log_id,$params);
		
		return $res;
    }
	public function getPriceOfUsdt($data)
    {

        $params = [];
        $params['api_request_param'] = $data;
        $log_id = $this->addapilog2([],'get_price',$params);


        $decrypt = $this->Decrypt($data,$this->app_key);




        if(empty($decrypt)) {
            
            $res['status'] = 'failed';
            $res['code']   = '400'; 
            $res['msg']    = "Decryption failed";
            $params = [];
            $params['api_response'] = json_encode($res);
            $this->updateapilog2([],$log_id,$params);
            return $res;
        }

        $params = [];
        $params['raw_request'] = json_encode($decrypt);
        $this->updateapilog2([],$log_id,$params);
		
		//$this->addapilog($decrypt,"get_price");
       
		$datas = explode("#",$decrypt);

        $coin_code =  $datas[0];
		if(strtoupper($coin_code)=='USDT'){
			$coin_info = (new CoinClass)->getMarketPrice($coin_code);
		}
		else
		{
			$coin_info = (new MarketPriceClass)->getMarketPrice($coin_code);
		}


		//print_r($coin_info);exit;
		//$result = $this->callSql("SELECT id,value FROM coin WHERE coin_code='$coin_code' ",'row');
		//$coin_info = json_decode($coin_info,true);
		
		
		//$this->updateapilog(json_encode($coin_info));

        $params = [];
        $params['formatted_response'] = json_encode($coin_info);
        $this->updateapilog2([],$log_id,$params);
		
		$res = [];
	
        if(!empty($coin_info))
        {
			if($coin_info['status']=="success")
			{
				if(strtoupper($coin_code)=='USDT')
				{
					$this->query("UPDATE coin SET value='".$coin_info['message']."' WHERE coin_code='$coin_code'");
					$this->execute();
				}
				else
				{
					$this->query("UPDATE coin SET usdt_value='".$coin_info['message']."' WHERE coin_code='$coin_code'");
					$this->execute();
				}
				$res['status'] = 'success';
				$res['data']   = $coin_info['message']; 
				$res['code']	   = '200';
			}
			else
			{
				$res['status'] = 'failed';
				$res['code']   = '400'; 
				$res['msg']	   = "coin not found";
			}

        }
        else
		{
			$res['status']	='failed';
			$res['code']	="400";
			$res['msg']	   = "coin not found";
		}

        $params = [];
        $params['api_response'] = json_encode($res);
        $this->updateapilog2([],$log_id,$params);
		return $res;
    }

    public function Encrypt($data,$key='')
    {
		
        if(empty($key))
            $key = $this->secretKey;

        $encData = openssl_encrypt($data, 'DES-EDE3', $key, OPENSSL_RAW_DATA);

        $encData = base64_encode($encData);

        return $encData;
    }

    public function Decrypt($data,$key='')
    {
		
        if(empty($key))
            $key = $this->secretKey;

        $data = base64_decode($data);


        $decData = openssl_decrypt($data, 'DES-EDE3', $key, OPENSSL_RAW_DATA);

        

        return $decData;
    }

    private function secureCheck($data)
    {
        ksort($data);
        $verify = array();

        foreach ($data as $key => $value) {
            if($key == 'encryptKey')
                continue;

            $verify[$key] = $value;
        }

        if(!empty($verify))
        {
            $queryString = http_build_query($verify);
            $queryString = urldecode($queryString);

           $clientMd5 = $data['encryptKey'];

            $decryptResult = $this->Decrypt($clientMd5);
//echo $decryptResult;exit;
            if($queryString == $decryptResult)
                return 0;
            else
                return 6;
        }

        return 6;
    }


    private function checkParams($data,$api)
    {
        $loginArray = array("api_key"=>1,"mode"=>1,"username"=>1,"password"=>1,"encryptKey"=>1);
        $depositArray = array("api_key"=>1,"mode"=>1,"currency"=>1,"amount"=>1,"trans_id"=>1,"accountId"=>1,"encryptKey"=>1);

        if(!in_array($api,$this->apiMode))
            return 7;

        foreach (${$api.'Array'} as $key => $value) {
            $checkKey = array_key_exists($key,$data);
            if(empty($checkKey))
                return 4;

            if($value == 1)
            {
                if(empty($data[$key]))
                    return 5;
            }
        }

    }

    public function insertLog($data,$url='',$type)
    {
        //$mode = $data['mode'];
        $data = json_encode($data);
        $time = time();
        $this->query("INSERT INTO api_call_log SET log_type='$type',user_id='$this->userid',raw_request='$data',request_time='$time',created_at='$time'");
		$this->execute();
        $this->lastLog = $this->lastInsertId();

        return $this->lastLog;
    }

    public function editLog($data,$lastLog='')
    {
        $time = time();
        if(!empty($lastLog))
            $this->lastLog = $lastLog;
        
        if(!empty($this->lastLog))
            $this->query("UPDATE api_call_log SET api_response='$data',response_time='$time',updated_at='$time' WHERE id='$this->lastLog'");
			$this->execute();
    }
	
	public function addapilog($data,$type)
    {
        //$mode = $data['mode'];
		if($type=="confirm_Deposit"){$type=1;}else{$type=2;}
        
        $data = json_encode($data);
        $time = time();
        $this->query("INSERT INTO trans_api_call_log SET log_type='$type',user_id='0',raw_request='$data',request_time='$time',created_at='$time'");
		$this->execute();
        $this->lastLog1 = $this->lastInsertId();

        return $this->lastLog1;
    }


    public function addapilog2($data,$type,$ip)
    {
        

        //$mode = $data['mode'];
        /*f($type=="confirm_Deposit"){$type=1;}else{$type=2;}
        if($type=="recharge_itt"){$type=4;}*/

        if($type =="confirm_Deposit") {
            $type=1; 
        }
        else if($type == "recharge_itt") {
           
            $type=4; 
        }else{
            $type=2;    
        }

               
     /*   $data = json_encode($data);
        $time = time();
        $this->query("INSERT INTO trans_api_call_log SET log_type='$type',user_id='0',raw_request='$data',request_time='$time',created_at='$time'");
        $this->execute();
        $this->lastLog1 = $this->lastInsertId();

        return $this->lastLog1;*/



        $user_id = isset($ip['user_id']) ? $ip['user_id'] : 0;
        $coin_id = isset($ip['coin_id']) ? $ip['coin_id'] : 0;
        $log_type = isset($type) ? $type : 0;
        $raw_request = isset($ip['raw_request']) ? json_encode($ip['service_charge']) : "";
        $api_request_param = isset($ip['api_request_param']) ? json_encode($ip['api_request_param']) : "";
        $request_time = time();
        $request_ip = $_SERVER['REMOTE_ADDR'];
        $created_at = time();
       

        $query = "INSERT INTO trans_api_call_log (`user_id`,`coin_id`,`log_type`,`raw_request`,`api_request_param`,`request_time`,`request_ip`,`created_at`) VALUES (:user_id,:coin_id,:log_type,:raw_request,:api_request_param,:request_time,:request_ip,:created_at)";

        $this->query($query);
        $this->bind(':user_id', $user_id);
        $this->bind(':coin_id', $coin_id);
        $this->bind(':log_type', $log_type);
        $this->bind(':raw_request', $raw_request);
        $this->bind(':api_request_param', $api_request_param);
        $this->bind(':request_time', $request_time);
        $this->bind(':request_ip', $request_ip);
        $this->bind(':created_at', $created_at);
        $this->execute();

        $this->lastLog1 = $this->lastInsertId();

        return $this->lastLog1;
    }



    public function updateapilog2($data,$lastLog='',$ip)
    {

       
        if(!empty($lastLog))
            $this->lastLog1 = $lastLog;
        $user_id = isset($ip['user_id']) ? $ip['user_id'] : 0;
        $api_response = isset($ip['api_response']) ? $ip['api_response'] : '';
        $response_time = time();
        $updated_at = time();
        $updated_ip = $_SERVER['REMOTE_ADDR'];

        $ip['response_time'] = $response_time;
        $ip['updated_at'] = $updated_at;
        $ip['updated_ip'] = $updated_ip;
        $ip['formatted_response'] = isset($ip['formatted_response']) ? $ip['formatted_response'] : '';


        $query = [];

        foreach($ip as $key => $value) {
            if($value) {
                $query[] = " $key = '".$value."' ";    
            }
           
        }  

        
        $implode = '';
        if (!empty($query)) {
            $implode = implode(",",$query);



            $this->query("UPDATE trans_api_call_log SET $implode WHERE id = '$this->lastLog1'  ");

            $this->execute();
        }
        
       /* $updated_ip = $_SERVER['REMOTE_ADDR'];

        $time = time();
        if(!empty($lastLog))
            $this->lastLog = $lastLog;
        
        
        if(!empty($this->lastLog))
            $this->query("UPDATE trans_api_call_log SET api_response='$data',response_time='$time',updated_at='$time' WHERE id='$this->lastLog'");
            $this->execute();*/

        
        
            
    }



    public function updateapilog($data,$lastLog='')
    {
        $time = time();
        if(!empty($lastLog))
            $this->lastLog = $lastLog;
        
        
        if(!empty($this->lastLog))
            $this->query("UPDATE trans_api_call_log SET api_response='$data',response_time='$time',updated_at='$time' WHERE id='$this->lastLog'");
			$this->execute();
    }

    public function getExternalDepositDecryptedData($data)
    {
        $explodedArray = [];
        if(!empty($data)) {
            
            $decrypt = $this->Decrypt($data,$this->app_key);
        }
        if(!empty($decrypt)) {
           
           $explodedArray = explode('#',$decrypt);

        }
        return $explodedArray;




    }

    public function addapilogrechargeitt($data,$type,$userId)
    {
        //$mode = $data['mode'];
        if($type=="recharge_itt"){$type=4;}else{$type=0;}
        $data = json_encode($data);
        $time = time();
        $this->query("INSERT INTO trans_api_call_log SET log_type='$type',user_id='$userId',raw_request='$data',request_time='$time',created_at='$time'");
        $this->execute();
        $this->lastLog1 = $this->lastInsertId();

        return $this->lastLog1;
    }

    public function callCurl($data,$urlParam)
    {
        $url = "http://demotestivps.com/infinite_new/api".$urlParam;
		if($urlParam=="/deposit"){$type=2;}else{$type=1;}

        $this->insertLog($data,$url,$type);
    
        $headers = array( 
        "Accept: application/x-www-form-urlencoded",
         );
        
        $data = http_build_query($data);
        
        $ch = curl_init();
        //echo $url.$data;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
        //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $return = curl_exec($ch);// exit;
        curl_close($ch);
        
        //print_r(json_decode($return,true));
        $this->editLog($return);
        
        return  $return;
        
    }
    
}
