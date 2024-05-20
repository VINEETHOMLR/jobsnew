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
//use src\lib\commonClass;

class walletAPI extends walletClass{
    //put your code here
    
    public function __construct() {
        parent::__construct();
        $this->apiKey = "";
        $this->secretKey = "";
        $this->lastLog = "";
        $this->apiMode = array("login","deposit");
        $this->currencyAllow = array("BTC","ETH","USDT");

        // Wallet APP Key
        $this->app_key = "b0295a8427eebf9c0f835108ee08cb4a";

        //$this->cObj  = (new commonClass);
    }
    
    public function loginAPI($data)
    {
        $vendor = $this->checkVendor($data);
        if(!empty($vendor))
            return $vendor;

        $secure = $this->secureCheck($data);
        if(!empty($secure))
            return $secure;

        $api = $data['mode'];
        $params = $this->checkParams($data,$api);
        if(!empty($params))
            return $params;

        $username = $data['username'];
        $password = $data['password'];
        $md5Password = md5($password);

        $result = $this->db->callsql("SELECT * FROM user WHERE username='$username' AND password='$md5Password'",'row');
        if(empty($result))
            return 101;

        // $loginExist = $this->db->callsql("SELECT * FROM app_login_log WHERE user_id='$result[id]'",'row');
        // if(!empty($loginExist))
        //     return 102;

        $sessionid = session_id();
        $time = time();
        $ip = $_SERVER['REMOTE_ADDR'];
        $this->db->callsql("INSERT INTO app_login_log SET user_id='$result[id]',session_id='$sessionid',last_active_time='$time',login_time='$time',login_ip='$ip',login_status=0");

        $resultArray = 0;

        return $resultArray;
    }

    public function depositAPI($data)
    {
        $vendor = $this->checkVendor($data);
        if(!empty($vendor))
            return $vendor;

        $api = $data['mode'];
        $params = $this->checkParams($data,$api);
        if(!empty($params))
            return $params;

        $secure = $this->secureCheck($data);
        if(!empty($secure))
            return $secure;

        $currency       = $data['currency'];
        $amount         = $data['amount'];
        $trans_id       = $data['trans_id'];
        $accountId      = $data['accountId'];
        $user = $this->db->callsql("SELECT * FROM user WHERE username='$accountId'",'row');
        if(empty($user))
            return 201;

        $loginExist = $this->db->callsql("SELECT * FROM app_login_log WHERE user_id='$user[id]'",'row');
        if(empty($loginExist))
            return 206;

        if(!in_array($currency, $this->currencyAllow))
            return 202;

        if(!is_numeric($amount))
            return 203;

        if($amount <= 0)
            return 204;

        $checkTransaction = $this->db->callsql("SELECT * FROM transaction WHERE bitgo_txid='$trans_id'",'row');
        if(!empty($checkTransaction))
            return 205;

        $time =  time();

        $coin_id = $this->db->callsql("SELECT coin_id FROM coin WHERE coin_code='$currency'","value");

        $price = $this->getAllPrice($currency);
        if($price == 'error_no_coin')
            return 202;

        if($price == 'error')
            return 207;

        $this->db->callsql("INSERT INTO transaction SET user_id='$user[id]',trans_type=4,coin_id='$coin_id',amount='$amount',receiver_address='',"
            . " current_coin_value='$price',bitgo_txid='$trans_id',bitgo_s_txid='$this->apiKey',bitgo_status='',"
            . " trans_usd_value='',bitgo_fee='',history='',status='0',time='$time' ");

        $resultArray = 0;

        return $resultArray;
    }

    public function confirmDeposit($trans_id)
    {
        $url = "scOrder/checkTransId";

        $checkTransaction = $this->db->callsql("SELECT * FROM transaction WHERE bitgo_txid='$trans_id'",'row');
        if(empty($checkTransaction))
            return 301;

        if($checkTransaction['status'] == 2)
            return 302;

        $data = $trans_id.'#'.time();
        $encrypt = $this->Encrypt($data,$this->app_key);
        $array = array('transId'=> $encrypt);
        $result = $this->callCurl($array,$url);
        $dbResult = $result;
        $result = json_decode($result,true);

        if($result['code'] == '200')
        {
            $transaction_id = $checkTransaction['transaction_id'];
            $user_id = $checkTransaction['user_id'];
            $code = $result['code'];
            $time = time();
            $this->creditCompanyCreditFromCoin($user_id,$transaction_id);

            $this->db->callsql("UPDATE transaction SET status=2 , history='$dbResult',bitgo_status='$code',update_time='$time',update_ip='' WHERE transaction_id='$transaction_id'");

            return 0;
        }
        else
            return $result['code'];

    }

    public function creditCompanyCreditFromCoin($userId,$transactionDetailsId)
    {
        //Stop Leverage UpdaTE
        
        $trans = $this->db->callsql("SELECT * FROM transaction WHERE user_id='$userId' AND transaction_id='$transactionDetailsId'",'row');

        /* Convert into USD */
        $amount = $trans['amount'];
        $usdt_coin_value = $trans['current_coin_value'];
        $usdt_amount = $amount * $usdt_coin_value;
        $usd_value = $this->db->callsql("SELECT coin_value FROM coin WHERE coin_id='4'","value");

        $getAmount = $usdt_amount * $usd_value;
        
        // $this->updateLeverageCredit($userId,$getAmount,1);  //No need Until End Of August
        
        //Type 23 (Credit From Coin Deposit)

        if($getAmount > 0)
        {
            $this->addPCAWallet($userId,$getAmount,1,10,0,'Deposit##'.$transactionDetailsId);
        } 
        
    }

    public function getIttPrice()
    {
        $url = "getIttPrice";

        $data = 'ITT#'.time();
        $encrypt = $this->Encrypt($data,$this->app_key);
        $array = array('param'=> $encrypt);
        $result = $this->callCurl($array,$url);
        $dbResult = $result;
        $result = json_decode($result,true);

        if($result['code'] == '200')
        {
            $price = $result['data'];

            $this->db->callsql("UPDATE coin SET coin_value='$price' WHERE coin_id='2'");

            return $price;
        }
        else
            return "error";

    }

    public function getAllPrice($coin)
    {
        $url = "getPriceOfUsdt";

        $coindb = $this->db->callsql("SELECT * FROM coin WHERE coin_code='$coin'","row");
        if(empty($coindb))
        {
            return "error_no_coin";
        }

        if($coindb['coin_id'] == 4)
        {
            return $coindb['coin_value'];
        }

        $coin = strtoupper($coin);

        $data = $coin.'#'.time();
        $encrypt = $this->Encrypt($data,$this->app_key);
        $array = array('param'=> $encrypt);
        $result = $this->callCurl($array,$url);
        $dbResult = $result;
        $result = json_decode($result,true);

        if($result['code'] == '200')
        {
            $price = $result['data'];

            $this->db->callsql("UPDATE coin SET coin_value='$price' WHERE coin_id='$coindb[coin_id]'");

            return $price;
        }
        else
            return "error";

    }

    public function checkUser($username)
    {
        $url = "getCoinAddress";

        $data = $username.'#ITT#'.time();
        $encrypt = $this->Encrypt($data,$this->app_key);
        $array = array('param'=> $encrypt);
        $result = $this->callCurl($array,$url);
        $result = json_decode($result,true);

        return $result['code'];
    }

    public function withdrawITT($username,$amount,$transId)
    {

        $url = "rechargeItt";

        $data = $username.'#'.$amount.'#'.$transId.'#'.time();
        $encrypt = $this->Encrypt($data,$this->app_key);
        $array = array('param'=> $encrypt);
        $result = $this->callCurl($array,$url);
        $result = json_decode($result,true);

        return $result['code'];
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

            if($queryString == $decryptResult)
                return 0;
            else
                return 6;
        }

        return 6;
    }

    private function checkVendor($data)
    {
        $this->apiKey = $data['api_key'];
        $result = $this->db->callsql("SELECT * FROM wallet_client WHERE api_key='$this->apiKey' AND status=1",'row');

        if(empty($result))
            return 1;

        $this->secretKey = $result['secret_key'];
        $status = $result['status'];

        if($status == 2)
            return 3;

        $client_ip = $_SERVER['REMOTE_ADDR'];
        $whitelist = $result['whitelist_ip'];

        if(!empty($whitelist))
        {
            $whitelist = explode(',',$whitelist);

            if(!in_array($client_ip, $whitelist))
                return 2;
        }
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

    public function insertLog($data,$url='')
    {
        $mode = $data['mode'];
        $data = json_encode($data);
        $time = time();
        $this->db->callsql("INSERT INTO wallet_client_api_log SET type='$mode',user_id='0',request='$data',req_time='$time',url='$url'");

        $this->lastLog = $this->db->lastInsertId();

        return $this->lastLog;
    }

    public function editLog($data,$lastLog='')
    {
        $time = time();
        if(!empty($lastLog))
            $this->lastLog = $lastLog;
        
        if(!empty($this->lastLog))
            $this->db->callsql("UPDATE wallet_client_api_log SET response='$data',res_time='$time' WHERE id='$this->lastLog'");
    }

    public function callCurl($data,$urlParam)
    {
        //$url = "https://api.infinitewallet.io/api/wallet/".$urlParam;
        $url = "http://localhost/infinite_app/financial/".$urlParam;

        $this->insertLog($data,$url);
    
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
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
        //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $return = curl_exec($ch);// exit;
        curl_close($ch);



        //print_r(json_decode($return,true));
        //$this->editLog($return);
        
        return  $return;
        
    }
    
}
