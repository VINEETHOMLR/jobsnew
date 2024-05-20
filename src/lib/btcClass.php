<?php

//include_once 'Helper.php';
namespace src\lib;

use src\lib\Helper;
use inc\Raise;
use src\models\ApiCallLog;

class btcClass
{

    public function __construct($userId = '')
    {   
        global $raiseParams;

        $btc_config = isset($raiseParams['btc']) ? $raiseParams['btc']: [];

        $this->apiURL   = isset($btc_config['api_url'])?$btc_config['api_url']:"";
        $this->token    = isset($btc_config['token'])?$btc_config['token']:"";
        $this->json      = true;
        $this->method    = 'GET';

        $this->getSample = true; 

        if (!empty($userId) && $userId > 0)
            $this->userId = $userId;
        else
            $this->userId = 0;
    }

    /*
    public function getMarketPrice($coin)
    {

        $api = new ApiCallLog();

        $request_url = $coin . '/market/latest/';

        $req_id = $api->insertLog(1, $this->userId, 7, $request_url);

        $this->setMethod('GET');
        $raw_response = $this->callAPI($request_url);

        $res_decode    = json_decode($raw_response, true);

        if (!empty($res_decode) && isset($res_decode['price']) && !empty($res_decode['price']) ) {
            $array = array('status' => 'success', 'message' => $res_decode['price']);
        } else {
            $array = array('status' => 'error', 'message' => 'API Error');
        }

        $standard_response = json_encode($array);

        $api->updateLog($req_id, $raw_response, $standard_response);

        return json_encode($array);

        return $response;
    }
    */

    public function createAccount($coin, $wallet_id)
    {
        $this->api_type = 'create';

        $api = new ApiCallLog();

        $request_url = $coin . '/wallet/' . $wallet_id . '/address';
        $request = json_encode([]);

        $req_id = $api->insertLog(1, $this->userId, 1, $request_url);

        $this->setMethod('POST');
        $raw_response  = $this->callCurl($request_url,$request);

        $res_decode    = json_decode($raw_response, true);

        if (!empty($res_decode) && isset($res_decode['address']) && !empty($res_decode['address']) ) {
            $array = array('status' => 'success', 'message' => $res_decode['address']);
        } else {
            $array = array('status' => 'error', 'message' => 'API Error');
        }

        $standard_response = json_encode($array);

        $api->updateLog($req_id, $raw_response, $standard_response);

        return json_encode($array);
    }

    public function getBalance($coin, $wallet_id)
    {

        $this->api_type = 'balance';

        $api = new ApiCallLog();

        $request_url = $coin . '/wallet/' . $wallet_id;

        $req_id = $api->insertLog(1, $this->userId, 3, $request_url);

        $this->setMethod('GET');
        $raw_response  = $this->callCurl($request_url);

        $res_decode    = json_decode($raw_response, true);

        //if ($res_decode['error'] == null) {
        if (!empty($res_decode) && isset($res_decode['balance']) && !empty($res_decode['balance']) ) {
            $array = array('status' => 'success', 'message' => $res_decode['balance']);
        } else {
            $array = array('status' => 'error', 'message' => 'API Error');
        }

        $standard_response = json_encode($array);

        $api->updateLog($req_id, $raw_response, $standard_response);

        return json_encode($array);
    }

    public function getWalletTransaction($coin, $wallet_id, $tx_id)
    {

        $this->api_type = 'gettrans';

        $api = new ApiCallLog();

        $request_url = $coin . '/wallet/' . $wallet_id . '/transfer/' . $tx_id;

        $req_id = $api->insertLog(1, $this->userId, 3, $request_url);

        $this->setMethod('GET');
        $raw_response  = $this->callCurl($request_url);

        //$res_decode    = json_decode($raw_response, true);

        //$standard_response = json_encode($res_decode);

        $api->updateLog($req_id, $raw_response, $raw_response);

        return $raw_response;
    }



    public function sendTransaction($coin, $wallet_id, $amount, $address, $transactionId = 0)
    {

        $this->api_type = 'sendcoin';

        $api = new ApiCallLog();

        $request = ['address'=>$address,'amount'=>$amount,'sequenceId'=>$transactionId];

        $raw_request = json_encode($request);

        $request_url = $coin.'/wallet/'.$wallet_id.'/sendcoins';

        $req_id = $api->insertLog(1, $this->userId, 6, $raw_request);

        $raw_response  = $this->callCurl($request_url);

        $res_decode    = json_decode($raw_response, true);

        if (!empty($res_decode) && isset($res_decode['status']) && $res_decode['status'] == 'signed') {
            $array = array('status' => 'success', 'message' => $res_decode['txid']);
        } else {
            $array = array('status' => 'error', 'message' => 'API Error');
        }

        $standard_response = json_encode($array);

        $api->updateLog($req_id, $raw_response, $standard_response);

        return json_encode($array);
    }


    private function callCurl($url,$request_json ="")
    {

        $curl = curl_init();

        $url =  $this->apiURL . $url;

        $header    = [
            "Authorization: Bearer $this->token",
            "Content-Type: application/json"
        ];

        $ch = curl_init($url);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $req_time = time();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        if ($this->method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request_json);
        } else if ($this->method == 'GET') {
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
        }
        
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
            case 'create' : 
                $return = '{
                              "id": "59cd72485007a239fb00282ed480da1f",
                              "address": "2MvrwRYBAuRtPTiZ5MyKg42Ke55W3fZJfZS",
                              "chain": 1,
                              "index": 0,
                              "coin": "string",
                              "lastNonce": -1,
                              "wallet": "59cd72485007a239fb00282ed480da1f",
                              "coinSpecific": {
                                "xlm": {
                                  "memoId": "2000000",
                                  "rootAddress": "GCTTCPH4IIDK7P72FFAEJ3ZFN6WDHJH6GGMRPHPM56ZWGIQ7B3XTIJAM"
                                },
                                "txlm": {
                                  "memoId": "2000000",
                                  "rootAddress": "GCTTCPH4IIDK7P72FFAEJ3ZFN6WDHJH6GGMRPHPM56ZWGIQ7B3XTIJAM"
                                }
                              },
                              "label": "Bobs Hot Wallet Address",
                              "addressType": "p2sh"
                            }'; 
                break;
            case 'balance' :
                $return = '{"balance": 50000,
                            "confirmedBalance": 40000,
                            "confirmedBalanceString": "40000",
                            "spendableBalance": 40000,
                            "stakedBalance": 40000,
                            "stakedBalanceString": "40000"}';
                break;
            case 'sendcoin' :
                $return = '{"txid": "458968999999",
                            "tx": "GCdddTTCPH4IIDK7P72FFAEJ3ZddddddFN6WDHJH6GGMRPHPM56ZWGIQ7B3XTIJAM",
                            "status": "signed"}';
                break;
            case 'gettrans' :
                $return =  '{"id":"5a408b15fe4aa8550720e22f9369e845","coin":"tbtc","wallet":"5a3ffad166b5ae4607a85b2d0632545d","txid":"34f1513f6f6b4791750cc5e26f8c281f7b1533e98fea308a16697242a7fe6af0","height":1255896,"date":"2017-12-25T05:31:38.882Z","confirmations":6,"value":100.12566589567777,"valueString":"100.12566589","feeString":"224756","payGoFee":0,"payGoFeeString":"0","usd":-32.0759799624,"usdRate":13663.54,"state":"confirmed","tags":["5a3ffad166b5ae4607a85b2d0632545d"],"history":[{"date":"2017-12-25T05:31:38.882Z","action":"confirmed"},{"date":"2017-12-25T05:22:36.156Z","action":"unconfirmed"},{"date":"2017-12-25T05:22:31.112Z","action":"signed"},{"date":"2017-12-25T05:22:29.728Z","action":"created"}],"vSize":373,"nSegwitInputs":0,"entries":[{"address":"2Mshr8JwXyPurdRigVnmYR8juEd7HSMLUwj","wallet":"5a3ffad166b5ae4607a85b2d0632545d","value":-100000000,"valueString":"-100000000","isChange":false,"isPayGo":false},{"address":"2NDRRMijg6mFaVA6JSvnnGo359v1dqoZqir","wallet":"5a3ffad166b5ae4607a85b2d0632545d","value":99765244,"valueString":"99765244","isChange":true,"isPayGo":false},{"address":"2MvtCrM5av4HDQxvwATujXprBaD5VpuNKT2","value":10000,"valueString":"10000","isChange":false,"isPayGo":false}],"confirmedTime":"2017-12-25T05:31:38.882Z","unconfirmedTime":"2017-12-25T05:22:36.156Z","signedTime":"2017-12-25T05:22:31.112Z","createdTime":"2017-12-25T05:22:29.728Z","outputs":[{"id":"34f1513f6f6b4791750cc5e26f8c281f7b1533e98fea308a16697242a7fe6af0:0","address":"2MvtCrM5av4HDQxvwATujXprBaD5VpuNKT2","value":10000,"valueString":"10000","isSegwit":false},{"id":"34f1513f6f6b4791750cc5e26f8c281f7b1533e98fea308a16697242a7fe6af0:1","address":"2NDRRMijg6mFaVA6JSvnnGo359v1dqoZqir","value":99765244,"valueString":"99765244","wallet":"5a3ffad166b5ae4607a85b2d0632545d","chain":1,"index":1,"redeemScript":"5221031988bf66743a6fe6a7a0e9c1e902e3a7e58def6a17fec0a34de199867aa0760121021124743d0d17924b73a8bb699ca31a3b79e34f34992cf74b320f57549e192f9a21034482de97044572038058e27be47420d1ff84cae0ab72dc9a4e0fc57458a0721853ae","isSegwit":false}],"inputs":[{"id":"366ac939f00858f91f34e9ef50a42c596948b60fe3942e1e58be2e95bef45707:0","address":"2Mshr8JwXyPurdRigVnmYR8juEd7HSMLUwj","value":100000000,"valueString":"100000000","wallet":"5a3ffad166b5ae4607a85b2d0632545d","chain":0,"index":0,"redeemScript":"5221032d8b66ec807985c94732b986fef4566f75ce0798a6e4be3aa13fd4e863b0e08521022cefa2a3fcc27c3d8451cd190018ab910d6a8bc5180b992e161c03117ce37b9e2103648e17c3db9bc8c6920701b0ae9d8b3205f60d7ea002487f39c93ac0415ee0b653ae","isSegwit":false}]}';
                break;
            default:
                $return = '';
                break;
        }

        return $return;
    }
}
