<?php

//include_once 'Helper.php';
namespace src\lib;

use src\lib\Helper;
use inc\Raise;
use src\models\ApiCallLog;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class ethClass
{
    public function __construct($userId = '')
    {
        /*if(empty($userId))
        {
            $this->userId = $_SESSION['USERID'];
        }
        else
        {
            $this->userId = $userId;
        }
        */

        $this->userId = $userId;

        //$this->apiurl = 'http://10.34.96.4:9090/api/';
        $this->apiurl = 'http://localhost/api/';

        $this->secretkey = 'jDjyrTEX5ur07zrsOT0n4OT0Tm8L9H6S';

        $this->method = 'AES-256-CBC';

        $this->etherscankey = '';

        $this->etherscanurl = 'https://api.etherscan.io/api?';


        $this->H = new Helper($this->secretkey, $this->method);
    }

    public function getCoinPrice($coinId) //For ETH
    {
        //module=stats&action=ethprice&apikey=YourApiKeyToken

        $requestUrl = 'https://api.coinbase.com/v2/prices/' . $coinId . '-USD/spot';

        if ($coinId == 'USDT')
            $requestUrl = 'https://api-pub.bitfinex.com/v2/tickers?symbols=tUSTUSD';

        $response = file_get_contents($requestUrl);

        $rep = json_decode($response, true);

        if ($coinId == 'USDT')
            $rep['data']['amount'] = $rep[0][7];

        if (!empty($rep['data']['amount'])) {
            $array = array('status' => 'success', 'message' => $rep['data']['amount']);
        } else {
            $array = array('status' => 'error', 'message' => 'Something Went Wrong..');
        }

        return json_encode($array);
    }


    public function getPublicTransactionList($address) //ETH
    {
        $requestUrl = $this->etherscanurl . 'module=account&action=txlist&address=' . $address . ''
            . '&startblock=0&endblock=99999999&sort=asc&apikey=' . $this->etherscankey;

        $requestUrl = $this->etherscanurl . 'module=account&action=txlist&address=' . $address . '&startblock=0&endblock=99999999&sort=asc&apikey=' . $this->etherscankey;
        $response = file_get_contents($requestUrl);
        //echo $response;
        $rep = json_decode($response, true);

        if ($rep['status'] == 1) {
            $array = array('status' => 'success', 'message' => $rep['result']);
        } else {
            $array = array('status' => 'error', 'message' => 'Something Went Wrong..');
        }

        return json_encode($array);
    }



    public function getPublicTransactionListToken($address)
    {

        $requestUrl = $this->etherscanurl . 'module=account&action=tokentx&address=' . $address . '&startblock=0&endblock=999999999&sort=asc&apikey=' . $this->etherscankey;

        $req_time = time();

        $response = file_get_contents($requestUrl);

        $time = time();

        $ip = $_SERVER['REMOTE_ADDR'];

        //callsql("INSERT INTO coin_api_log  SET user_id='$this->userId',type=2,server='$requestUrl',request='$requestUrl',response='$response',req_time='$req_time',res_time='$time',ip='$ip'");

        //echo $response;
        $rep = json_decode($response, true);

        if ($rep['status'] == 1) {
            $array = array('status' => 'success', 'message' => $rep['result']);
        } else {
            $array = array('status' => 'error', 'message' => 'Something Went Wrong..');
        }

        return json_encode($array);
    }


    public function createAccount($accountName)
    {
        $api = new ApiCallLog();

        $service = 'api/createWallet';

        $array  = array('accountName' => $accountName);

        $enc =  json_encode($array);

        $data = $this->H->encrypt($enc);

        $new_url  = $this->apiurl . $service;

        $request = array('data' => $data);

        $request = http_build_query($request); 

        $req_id = $api->insertLog(2, $this->userId, 1, $request); 

        $response = $this->callCurl($new_url, $request);

        $res_decode = json_decode($response, true);

        if ($res_decode['status'] == true) {

            $decode = $this->H->decrypt($res_decode['data']);

            $decode = json_decode($decode, true);

            if ($decode['status'] == 'success')
                $array = array('status' => 'success', 'message' => $decode['data']['address']);
            else
                $array = array('status' => 'error', 'message' => $decode['msg']);
        } else {
            $array = array('status' => 'error', 'message' => 'API Error');
        }

        $standard_response = json_encode($array);

        $api->updateLog($req_id, $response, $standard_response);

        return json_encode($array);
    }

    public function getBalance($address, $walletType = 'ETH')
    {

        $api = new ApiCallLog();

        if ($walletType == 'ETH')
            $service = 'api/checkBalance';
        else
            $service = 'api/getTokenBalance';

        $array  = array('walletAddress' => $address);

        if ($walletType != 'ETH')
            $array['tokenCode'] = $walletType;

        $enc =  json_encode($array);

        $data = $this->H->encrypt($enc);

        $new_url  = $this->apiurl . $service;
        //$new_url  = 'http://104.238.220.93:9090/api/' . $service;

        $request = array('data' => $data);

        $request = http_build_query($request);

        $req_id = $api->insertLog(2, $this->userId, 3, $request);

        $response = $this->callCurl($new_url, $request);

        $res_decode = json_decode($response, true);

        if ($res_decode['status'] == true) {

            $decode = $this->H->decrypt($res_decode['data']);

            $decode = json_decode($decode, true);

            if ($decode['status'] == 'success')
                $array = array('status' => 'success', 'message' => $decode['data']['balance']);
            else
                $array = array('status' => 'error', 'message' => $decode['msg']);
        } else {
            $array = array('status' => 'error', 'message' => 'API Error');
        }

        $standard_response = json_encode($array);

        $api->updateLog($req_id, $response, $standard_response);

        return json_encode($array);
    }


    public function transferCoin($fromAddress, $toAddress, $quantity, $walletType = 'ETH', $transId)
    {
        if ($walletType == 'ETH')
            $service = 'api/sendTransaction';
        else
            $service = 'api/sendTokenTransaction';

        $array  = array('from' => $fromAddress, 'to' => $toAddress, 'value' => $quantity, 'transId' => $transId);

        if ($walletType != 'ETH')
            $array['tokenCode'] = $walletType;

        $enc =  json_encode($array);

        $data = $this->H->encrypt($enc);

        $new_url  = $this->apiurl . $service;

        $request = array('data' => $data);

        $request = http_build_query($request);

        $response = $this->callCurl($new_url, $request);

        $res_decode = json_decode($response, true);

        if ($res_decode['status'] == true) {

            $decode = $this->H->decrypt($res_decode['data']);

            $decode = json_decode($decode, true);

            if ($decode['status'] == 'success')
                $array = array('status' => 'success', 'message' => $decode['data']['hash']);
            else
                $array = array('status' => 'error', 'message' => $decode['msg']);
        } else {
            $array = array('status' => 'error', 'message' => 'API Error');
        }

        return json_encode($array);
    }

    /* Batch Income Withdrawal api */
    public function transferCoinBatch($from_address, $batch_id, $transactions = [])
    {

        if (empty($from_address) || empty($batch_id) || count($transactions) <= 0) {
            $result = array('status' => 'error', 'message' => 'Invalid Parameters');
            return json_encode($result);
        }

        $api = new ApiCallLog();

        $service = 'api/BulkTransaction';
        $new_url  = $this->apiurl . $service;
        //$new_url  = 'http://104.238.220.93:9090/api/' . $service;

        $array   = array('from_address' => $from_address, 'batch_id' => $batch_id, 'transactions' => $transactions);
        $json    = json_encode($array); 
        $enc     = $this->H->encrypt($json);
        $request = http_build_query(['data'=>$enc]); 

        $req_id = $api->insertLog(2, $this->userId, 3, $request);

        $response = $this->callCurl($new_url, $request);

        /*
        $response = '{"status":true,"data":"a10e239dfc950f181afa6baa77930145mwF73v0fJro4R4SCO4XxcaCnB8FnLL2B55pypZZ8Bh4MVyk82IL+Vw=="}';
        */

        $res_decode = json_decode($response, true);

        if (empty($res_decode)) {

            $result = array('status' => 'error', 'message' => 'API Error - Empty Response');

            $standard_response = json_encode($result);

            $api->updateLog($req_id, $response, $standard_response);

            return json_encode($result);
        }

        if ($res_decode['status'] == true) {

            $decode = $this->H->decrypt($res_decode['data']); 

            /*
            $decode = '{"status":"success","message":"Successss"}'; 
            */

            $decode = json_decode($decode, true);

            $message = isset($decode['message']) ? $decode['message'] : "API Error - Empty Msg";

            if ($decode['status'] == "success") {
                $result = array('status' => 'success', 'message' => $message);
            } else {
                $result = array('status' => 'error', 'message' => $message);
            }

        } else {

            $result = array('status' => 'error', 'message' => 'API Error');
        }

        $standard_response = json_encode($result);

        $api->updateLog($req_id, $response, $standard_response);

        return json_encode($result);
    }

    /*Get trans Fee */
    public function getTransFee() //For ETH
    {  

        $api = new ApiCallLog();

        $req_id = $api->insertLog(2, $this->userId, 3, "");

        $service = 'api/GetGasPrice';
        $requestUrl  = $this->apiurl . $service;
        //$requestUrl  = 'http://104.238.220.93:9090/api/' . $service;

        $response = file_get_contents($requestUrl);

        $res_decode = json_decode($response, true);

        if (empty($res_decode)) {
            $result = array('status' => 'error', 'message' => 'API Error - Empty Response');

            $standard_response = json_encode($result);

            $api->updateLog($req_id, $response, $standard_response);

            return json_encode($result);
        }

        if ($res_decode['status'] == true) {

            $decode = $this->H->decrypt($res_decode['data']); 

            /*
            $decode = '{"status":"success","data":{"gasPrice":"20.00"}}';
            */

            $decode = json_decode($decode, true);

            $gasPrice = isset($decode['data']['gasPrice']) ? $decode['data']['gasPrice'] : "0.00";

            if ($decode['status'] == "success") {
                $result = array('status' => 'success', 'message' => $gasPrice);
            } else {
                $result = array('status' => 'error', 'message' => 'API Error');
            }

        } else {

            $result = array('status' => 'error', 'message' => 'API Error');
        }

        $standard_response = json_encode($result);

        $api->updateLog($req_id, $response, $standard_response);

        return json_encode($result);
    }


    public function transferHistory($address, $walletType)
    {
        if ($walletType == 'ETH')
            $service = 'api/getTransByAddress';
        else
            $service = 'api/getTokenTransByAddress';

        $array  = array('walletAddress' => $address);

        if ($walletType != 'ETH')
            $array['tokenCode'] = $walletType;

        $enc =  json_encode($array);

        $data = $this->H->encrypt($enc);

        $new_url  = $this->apiurl . $service;

        $request = array('data' => $data);

        $request = http_build_query($request);

        $response = $this->callCurl($new_url, $request);

        $res_decode = json_decode($response, true);

        if ($res_decode['status'] == true) {

            $decode = $this->H->decrypt($res_decode['data']);

            $decode = json_decode($decode, true);

            if ($decode['status'] == 'success')
                $array = array('status' => 'success', 'message' => $decode['data']);
            else
                $array = array('status' => 'error', 'message' => $decode['msg']);
        } else {
            $array = array('status' => 'error', 'message' => 'API Error');
        }

        return json_encode($array);
    }


    protected function callCurl($url, $request)
    {
        $ch = curl_init($url);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $req_time = time();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60); //Timeout
        $return = curl_exec($ch);

        $time = time();
        $ip = $_SERVER['REMOTE_ADDR'];

        //echo "INSERT INTO coin_api_log SET user_id='$this->userId',type=1,server='$url',request='$request',response='$return',req_time='$req_time',res_time='$time',ip='$ip'";

        //callsql("INSERT INTO coin_api_log  SET user_id='$this->userId',type=1,server='$url',request='$request',response='$return',req_time='$req_time',res_time='$time',ip='$ip'");

        return $return;
    }
}
