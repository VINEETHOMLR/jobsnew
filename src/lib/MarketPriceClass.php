<?php

//include_once 'Helper.php';
namespace src\lib;

use src\lib\Helper;
use inc\Raise;
use src\models\ApiCallLog;

class MarketPriceClass
{

    public function __construct($userId = '')
    {   

        $this->apiURL   = TRADE_API_HTTP_URL;
        $this->token    = '';
        $this->tradeUserId = TRADE_API_USER_ID;
        $this->authCode  = TRADE_API_AUTH_CODE;

        // $this->apiURL  = 'https://api.testexchange.com/api/';
        // $this->token   = 'testauth';
        // $this->tradeUserId = '1000'; //dummy
        // $this->authCode  = 'testauth';

        $this->getSample = false; 
        $this->json      = true;
        $this->method    = 'POST';

        if (!empty($userId) && $userId > 0)
            $this->userId = $userId;
        else
            $this->userId = 0;
    }

    public function auth() {

        $this->api_type = 'auth';

        $api = new ApiCallLog();

        $request_url = 'auth';

        $request = ['cmd'=>'auth.login','user_id'=>$this->tradeUserId,'auth_code'=>$this->authCode];

        $req_id = $api->insertLog(1, 0, 10, json_encode($request));

        $raw_response = $this->callCurl($request_url,$request);

        $res_decode  = json_decode($raw_response, true);

        if (!empty($res_decode) && isset($res_decode['result']) && !empty($res_decode['result']) ) {
            $this->api_token_received = $res_decode['result']['api_token'];

            $array = array('status' => 'success', 'message' => $this->api_token_received);
        } else {
            $array = array('status' => 'error', 'message' => 'API Error');
        }

        $standard_response = json_encode($array);

        $api->updateLog($req_id, $raw_response, $standard_response);

    }

    
    public function getMarketOrderPrice($coin,$side=1)
    {

        $this->api_type = 'order';

        $api = new ApiCallLog();

        $request_url = 'market';

        $request = ['cmd'=>'order.book','market'=>strtoupper($coin).'-USDT','side'=>$side,'offset'=>0,'limit'=>1];

        $req_id = $api->insertLog(1, $this->userId, 11, json_encode($request));

        $raw_response = $this->callCurl($request_url,$request);

        $res_decode    = json_decode($raw_response, true); 

        if (!empty($res_decode) && isset($res_decode['result']) && !empty($res_decode['result']) ) {

            $order_details = $res_decode['result']['orders'];

            $last_order = array_pop($order_details);

            $price = $last_order['price'];
            $amount = $last_order['amount'];

            $array = array('status' => 'success', 'message' => $price,'data'=>['amount'=>$amount]);
        } else {
            $array = array('status' => 'error', 'message' => 'API Error');
        }

        $standard_response = json_encode($array);

        $api->updateLog($req_id, $raw_response, $standard_response);

        return $array;
    }

    public function getMarketPrice($coin) {

        //$auth = $this->auth();

        $sell_price_info = $this->getMarketOrderPrice($coin,1);

        if ($sell_price_info['status'] == 'error') {
            return ['status'=>'error','message' => 'API Error'];
        }

        $sell_price = $sell_price_info['message'];

        if ($sell_price <= 0) {
            return ['status'=>'error','message' => 'API Error'];
        }

        $sell_amount = $sell_price_info['data']['amount'];

        $buy_price_info = $this->getMarketOrderPrice($coin,2);

        if ($buy_price_info['status'] == 'error') {
            return ['status'=>'error','message' => 'API Error'];
        }

        $buy_price = $buy_price_info['message'];

        if ($buy_price <= 0) {
            return ['status'=>'error','message' => 'API Error'];
        }

        $buy_amount = $buy_price_info['data']['amount'];

        $org_price = ($sell_price + $buy_price)/2;

        //for coin price history
        $data['high'] = max($buy_price,$sell_price);
        $data['low']  = min($buy_price,$sell_price);
        $data['latest'] = $org_price;
        $data['volume'] = $sell_amount + $buy_amount;

        return ['status'=> 'success','message'=>$org_price,'data'=>$data];
    }


    private function callCurl($url,$request_json ="")
    {

        $curl = curl_init();

        $url =  $this->apiURL .'/'. $url; 

        $header    = [
            "Content-Type: application/json"
        ];

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        if ($this->method == 'POST') { 
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request_json));
        } else if ($this->method == 'GET') {
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
        }

        //curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300); //Timeout after 7 seconds

        $return = curl_exec($ch);  

        curl_close($curl);

        if ($this->getSample == true) {
            $return = $this->getSampleResponse();
        }

        if ($this->json  === true) {
            return $return;
        }

        $responseApi = json_decode($return, true);

        if (empty($responseApi))
            $responseApi = $return;

        return $responseApi;

    }

    public function setJson($status)
    {
        $this->json  = $status;
    }

    public function setMethod($method)
    {

        $this->method  = $method;
    }

    public function getSampleResponse(){

        switch ($this->api_type) {
            case 'auth' : 
                $return = '{"result": {
                            "api_token": "h6fbLhCkTlYOPiWRzXadqeqjSTgu9j"
                            }}'; 
                break;
            case 'order' :
                $return = '{"result": {
                                        "offset": 0,
                                        "limit": 1,
                                        "total": 5,
                                        "orders": [
                                                    {
                                                    "id": 516,
                                                    "market": "BTC-USDT",
                                                    "type": 1,
                                                    "side": 1,
                                                    "user": 4,
                                                    "ctime": 1556517459.197866,
                                                    "mtime": 1556517459.197866,
                                                    "price": "1500.031",
                                                    "amount": "1.50",
                                                    "taker_fee": "0.003",
                                                    "maker_fee": "0.0015",
                                                    "left": "1",
                                                    "deal_stock": "0e-8",
                                                    "deal_money": "0e-16",
                                                    "deal_fee": "0e-12"
                                                    }]
                                    }}';
                break;
            
            default:
                $return = '';
                break;
        }

        return $return;
    }
}
