<?php

namespace src\lib;

use inc\Raise;
use src\lib\RRedis;
use src\lib\Database;
use src\models\User;
use src\lib\ethClass;
use src\lib\btcClass;
use src\models\Coin;
use src\models\SiteData;
use src\models\CoinPriceHistory;
use src\lib\MarketPriceClass;

class CoinClass extends Database
{
    public function __construct($db = 'db')
    {
        parent::__construct(Raise::params()[$db]);
    }

    public function createAddress($user_id,$coin_code)
    {

        $user_info = (new User)->getUserDetails($user_id);

        $username = $user_info['username']; 

        if ($coin_code == 'btc') {

            $coin_info = (new Coin)->getByCoinCode($coin_code);

            $wallet_id = isset($coin_info['wallet_id'])?$coin_info['wallet_id']:"";

            $result = (new btcClass($user_id))->createAccount("btc",$wallet_id);
            //$result = '';
        } else {
            //$result = (new ethClass($user_id))->createAccount($username); 
            $result = '';
        }

        if (empty($result)) {
            $result = ["status"=>"success","message"=>"asdfasdfasdf".$coin_code."xyz".$user_id."asdfasdfasdf"];
            $result = json_encode($result);
        }

        $response = json_decode($result,true);

        return $response;
    }

    public function getMarketPrice($coin_code) {

        $result = (new ethClass())->getCoinPrice(strtoupper($coin_code));

        return json_decode($result,true);
    }

    public function getTradeMarketPrice($coin_code) {

        if ($coin_code != 'usdt' && $coin_code != 'USDT') {
            $result = (new MarketPriceClass)->getMarketPrice($coin_code);

            return $result;
        } else {
            return ['status'=>'error','message' => 'API Error'];
        }
    }

    public function updateTradeMarketPrice() {

        $coin_list = (new Coin)->getCoinDetails();

        //$coin_list = array_column($coin_details, NULL,'coin_id');

        foreach($coin_list as $coin_info){

            if ($coin_info['coin_code'] != 'usdt'){

                $coin_id = $coin_info['id'];
                $coin_code = $coin_info['coin_code'];

                $result = (new MarketPriceClass)->getMarketPrice($coin_code);

                if ($result['status'] == 'success' && $result['message'] > 0) {

                    $last_usdt_value = $coin_info['last_usdt_value'];
                    $new_usdt_value  = $result['message'];

                    $per = ($new_usdt_value - $last_usdt_value)/100;

                    $change_per = $per;
                    // if ($per < 0){
                    //     $change_per = $per;
                    // } else {
                    //     $change_per = $per;
                    // }

                    $update['last_usdt_value'] = ($coin_info['usdt_value'] > 0) ? $coin_info['usdt_value'] : $result['message'];
                    $update['usdt_value'] = $result['message'];
                    $update['percentage'] = $change_per;
                    $update['difference'] = $new_usdt_value - $last_usdt_value;

                    $twohourPrice = (new CoinPriceHistory)->getLast24HourPrice($coin_id);

                    $twohourPrice = ($twohourPrice > 0) ? $twohourPrice: $new_usdt_value;
                    $update['24_hour_price'] = ($new_usdt_value - $twohourPrice)/100;

                    (new Coin)->updateCoin($coin_id,$update);

                    $insert['coin_id'] = $coin_id;
                    $insert['coin_code'] = $coin_code;
                    $insert['price'] = $new_usdt_value;
                    $insert['percentage'] = $change_per;
                    $insert['diff'] = $new_usdt_value - $last_usdt_value;

                    $insert['open_price'] = $last_usdt_value;
                    $insert['latest_price'] = $result['data']['latest'];
                    $insert['highest_price'] = $result['data']['high'];
                    $insert['lowest_price'] = $result['data']['low'];
                    $insert['volume'] = $result['data']['volume'];

                    $insert['time'] = time();
                    $insert['created_at'] = time();
                    $insert['created_ip'] = getClientIp();

                    (new CoinPriceHistory)->assignAttrs($insert)->save();
                }
            }
        }
    }

    public function getBalance($coin_code)
    {

        if ($coin_code == 'btc') {

            $coin_info = (new Coin)->getByCoinCode($coin_code);
            $wallet_id = isset($coin_info['wallet_id'])?$coin_info['wallet_id']:"";

            $result = (new btcClass())->getBalance("btc",$wallet_id);
            //$result = '';
        } else {

            $eth_master_address = (new SiteData)->getSiteData("eth_masteraddress");

            $result = (new ethClass())->getBalance($eth_master_address,strtoupper($coin_code)); 
            //$result = '';
        }

        /*
        if (empty($result)) {
            $result = ["status"=>"success","message"=>"100.000"];
            $result = json_encode($result);
        }
        */

        $response = json_decode($result,true);

        return $response;
    }

    public function sendCoin($user_id,$coin_code,$amount,$toAddress,$fromAddress='',$id) {

        if($coin_code == 'btc'){

            $coin_info = (new Coin)->getByCoinCode($coin_code);
            $wallet_id = isset($coin_info['wallet_id'])?$coin_info['wallet_id']:"";
           
            $btc_trans = (new btcClass($user_id))->sendTransaction('btc',$wallet_id,$amount, $toAddress, $id);

            $response = json_decode($btc_trans, true);

        }else{

            $coin_code = strtoupper($coin_code);

            $eth_trans  = (new ethClass($user_id))->transferCoin($fromAddress, $toAddress, $amount, $coin_code, $id);

            $response = json_decode($eth_trans, true);
        }

        return $response;
    }

    public function verifyAddress($user_id,$coin_code,$address)
    {

        if ($coin_code == "btc") {
            $isValid = $this->validateBTC($address);
        } else {
            $isValid = $this->isAddress($address);
        }

        return $isValid;
    }


    /**
     * Checks if the given string is an address
     *
     * @param String $address the given HEX adress
     * @return Boolean
    */
    public function isAddress($address) {

        if (preg_match('/^(0x)?[0-9a-fA-F]{40}$/', $address)) {
            return true;
        } else {
            return false;
        }

        // if (!preg_match('/^(0x)?[0-9a-f]{40}$/i',$address)) {
        //     // Check if it has the basic requirements of an address
        //     return false;
        // } elseif (preg_match('/^(0x)?[0-9a-f]{40}$/',$address) || preg_match('/^(0x)?[0-9A-F]{40}$/',$address)) {
        //     // If it's all small caps or all all caps, return true
        //     return true;
        // } else {
        //     // Otherwise check each case
        //     return $this->isChecksumAddress($address);
        // }
    }

    /**
     * Checks if the given string is a checksummed address
     *
     * @param String $address the given HEX adress
     * @return Boolean
    */
    public function isChecksumAddress($address) { 
        // Check each case
        $address = str_replace('0x','',$address); 
        $addressHash = hash('sha3',strtolower($address));
        $addressArray=str_split($address);
        $addressHashArray=str_split($addressHash);

        for($i = 0; $i < 40; $i++ ) {
            // The nth letter should be uppercase if the nth digit of casemap is 1
            if ((intval($addressHashArray[$i], 16) > 7 && strtoupper($addressArray[$i]) !== $addressArray[$i]) || (intval($addressHashArray[$i], 16) <= 7 && strtolower($addressArray[$i]) !== $addressArray[$i])) {
                return false;
            }
        }
        
        return true;
    }


    //for bit coin
    public function validateBTC($address){
        $decoded = $this->decodeBase58($address);

        if (!$decoded) {
            return false;
        }
 
        $d1 = hash("sha256", substr($decoded,0,21), true);
        $d2 = hash("sha256", $d1, true);
 
        if(substr_compare($decoded, $d2, 21, 4)){
            return false;
        }
        return true;
    }

    public function decodeBase58($input) {
        $alphabet = "123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz";
 
        $out = array_fill(0, 25, 0);
        for($i=0;$i<strlen($input);$i++){
                if(($p=strpos($alphabet, $input[$i]))===false){
                        return false;
                }
                $c = $p;
                for ($j = 25; $j--; ) {
                        $c += (int)(58 * $out[$j]);
                        $out[$j] = (int)($c % 256);
                        $c /= 256;
                        $c = (int)$c;
                }
                if($c != 0){
                    return false;
                }
        }
 
        $result = "";
        foreach($out as $val){
                $result .= chr($val);
        }
 
        return $result;
    }



}
