<?php

namespace src\lib;

use inc\Raise;


class Router {

    /**
     * 
     * @param String $queryString
     * @return String
     */
    public static function get($queryString = '') {
        //Check for Pretty URL
        $params = Raise::params();
        $get = (array_key_exists('route', $params) && array_key_exists('prettyURL', $params['route']) && $params['route']['prettyURL'] === true) ?
                $GLOBALS['routes'] : $_GET;

        return isset($get[$queryString]) ? cleanMe($get[$queryString]) : '';
    }

    /**
     * 
     * @param String $queryString
     * @return String
     */
    public static function post($queryString = '') {
        return isset($_POST[$queryString]) ? cleanMe($_POST[$queryString]) : '';
    }

    /**
     * 
     * @param String $queryString
     * @return String
     */
    public static function req($queryString = '') {
        return isset($_REQUEST[$queryString]) ? cleanMe($_REQUEST[$queryString]) : '';
    }

    /**
     * Method to return the all GET method values
     * @return type
     */
    public static function getAll() {
        $params = Raise::params();
        //Check Pretty URL
        $get = (array_key_exists('route', $params) && array_key_exists('prettyURL', $params['route']) && $params['route']['prettyURL'] === true) ?
                $GLOBALS['routes'] : $_GET;
        return self::cleanAllValue($get);
    }

    /**
     * Method to return the all POST method values
     * @return type
     */
    public static function postAll() {
        return self::cleanAllValue(json_decode(file_get_contents('php://input', 'r'), true));
    }

    /**
     * 
     * @param String $queryString
     * @return String
     */
    public static function reqAll() {
        return self::cleanAllValue($_REQUEST);
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    public static function cleanAllValue($data = []) {
        $newArray = [];
        foreach ($data as $key => $val) {
            $newArray[$key] = is_array($val) ? self::cleanAllValue($val) : Raise::cleanMe($val);
        }
        return $newArray;
    }

    /**
     * 
     * @return String - Lower Case
     */
    public static function getReqMethod() {
        return isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : '';
    }

    /**
     * 
     * @return Boolean
     */
    public static function isAjaxReq() {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    /**
     * 
     * @param String $url
     * @return Mixed
     */
    public static function cGet($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
//                throw new Exception(curl_error($ch));
            $result = null;
        }
        curl_close($ch);
        return $result !== null ? self::rJSON($result) : $result;
    }

    /**
     * 
     * @param String $res
     * @return Mixed
     */
    public static function rJSON($res) {
        $response = json_decode($res, true);
        return (json_last_error() === JSON_ERROR_NONE) ? $response : $res;
    }

    /**
     * 
     * @param Array $data
     * @param String $url
     * @return Mixed
     */
    public static function cPost($data = [], $url = '') {
        $data_string = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $result = curl_exec($ch);
        curl_close($ch);
        return self::rJSON($result);
    }

}
