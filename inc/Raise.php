<?php

namespace inc;

use src\auth\Auth;
use src\lib\Role;
use src\lib\RRedis;
/**
 * Raise MVC Raise
 *se src\lib\Router;
 * 
 */
class Raise
{

    /**
     *
     * @var String ISO 639-1 Language Code
     */
    public static $lang = 'en';
    public static $csrf_token = '';
    public static $userObj = [];
    public static $reqHeader = [];
    public static $apiCallArr = [];
    public static $controllerAction = '';

    /**
     *
     * @var String
     */
    private $baseUrl = '';

    /**
     *
     * @var String
     */
    public $controllerPath = '\src\controllers\\';

    /**
     * Initial Functions related
     */
    public function initApp()
    {
        $this->initSession();
        $this->siteLang();
        $this->baseUrl = parse_url(BASEURL);
        $this->initCSRF();
        //$this->safeSession();
        $this->parseURI();
        //$this->parseAPI();

    }

    public static function init($userObj = [])
    {
        self::$userObj = $userObj;
    }

    public static function initLang($lang)
    {
        self::$lang = strtolower($lang);

    }

    public static function initReqHeader($reqHeader)
    {
        self::$reqHeader = $reqHeader;

        // print_r(self::$reqHeader);
    }

    /**
     * Method to generate the CSRF Token
     */
    protected function initCSRF()
    {
        if (!isset(self::$csrf_token) || empty(self::$csrf_token)) {
            $token = (function_exists('mcrypt_create_iv')) ? bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM)) : bin2hex(openssl_random_pseudo_bytes(32));
            self::$csrf_token = $token;
        }
        return self::$csrf_token;
    }

    /**
     * Method to initiate the session
     */
    public function initSession()
    {
        ob_start();
    }

    /**
     * Flush all once the request has completed
     */
    public function __destruct()
    {
        ob_flush();
    }

    /**
     *
     * @param String $forceLang
     * @return String the Language
     */
    public function siteLang($forceLang = '')
    {
        if (trim($forceLang) != '') {

            self::$lang = Raise::cleanMe($forceLang);
        }
        // Raise::$lang = $this->lang;
        // $_SESSION['lang'] = $this->lang;
        setlocale(LC_ALL, self::$lang);
    }

    /**
     *
     * @return Array
     */
    public static function getLang()
    {
        $langs = [];
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);
            if (count($lang_parse[1])) {
                $langs = array_combine($lang_parse[1], $lang_parse[4]);
                foreach ($langs as $lang => $val) {
                    if ($val === '') {
                        $langs[$lang] = 1;
                    }

                }
                arsort($langs, SORT_NUMERIC);
            }
        }
        return $langs;
    }

    /**
     *
     * @throws \Exception
     */
    private function parseURI()
    {
        $req = $_SERVER['REQUEST_URI'];
        $reqUrl = $this->extractURL($req);

        $ctrlCount = $this->baseUrl['path'] !== '/' ? 2 : 1;
        try {
            if (count($reqUrl) >= $ctrlCount && trim($reqUrl[1]) !== '') {
                
                if (extension_loaded ('newrelic')) {
                    newrelic_name_transaction ($_SERVER['REDIRECT_URL']);
                    if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
                        newrelic_add_custom_parameter ('QUERY_STRING', $_SERVER['QUERY_STRING']);
                    }
                }

                $ctrlName = array_key_exists('0', $reqUrl) ? ucfirst($reqUrl[0]) : 'Index';
                $ctlr = $this->controllerPath . $ctrlName . 'Controller';

                $authResult = (new Auth())->authApiToken();
                $uri = array_slice($reqUrl, 1);
                self::$controllerAction =  $uri[0]; // assign here for auth can detect action
                $ctlrIns = new $ctlr($authResult);
                return $this->parseAction($ctlrIns, $uri, strtolower($ctrlName));
            } else {
                throw new \Exception('Controller Not Found!', 404);
            }
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage(), 404);
        }
    }

    private function parseAPI()
    {
        print_r($_REQUEST);
    }

    /**
     *
     * @param RequestString $req
     * @return String
     */
    private function extractURL($req)
    {
        $reqPath = str_replace(array_key_exists('path', $this->baseUrl) && $this->baseUrl['path'] !== '/' ? $this->baseUrl['path'] : '', '', $req);
        $mvcPath = explode('?', ltrim($reqPath, '/'));
        $reqURI = explode('/', $mvcPath[0]);
        $reqUrl = array_filter($reqURI, function ($val) {
            return ($val !== null && $val !== false && $val !== '');
        });
        $params = self::params();
        if ((count($reqUrl) === 1 || count($reqUrl) === 0) && count($params) > 0 && array_key_exists('mvc', $params) && array_key_exists('defaults', $params['mvc']) && array_key_exists('controller', $params['mvc']['defaults'])) {
            $reqUrl[0] = (count($reqUrl) === 1) ? $reqUrl[0] : $params['mvc']['defaults']['controller'];
            $reqUrl[1] = array_key_exists('action', $params['mvc']['defaults']) ? $params['mvc']['defaults']['action'] : 'index';
        }
        $this->prettyURL($reqUrl);
        return $reqUrl;
    }

    /**
     * To enable user friendly Pretty URL
     * @param Array $reqUrl
     */
    private function prettyURL($reqUrl)
    {
        $params = self::params();
        if (count($params) > 0 && array_key_exists('route', $params) && array_key_exists('prettyURL', $params['route']) && $params['route']['prettyURL'] === true) {
            if (count($reqUrl) > 2) {
                unset($reqUrl[0]);
                unset($reqUrl[1]);
            }
            $GLOBALS['routes'] = [];
            $rChunks = array_chunk($reqUrl, 2);
            foreach ($rChunks as $k => $r) {
                if (count($r) === 2) {
                    $GLOBALS['routes'][$r[0]] = $r[1];
                } elseif (count($r) === 1) {
                    $GLOBALS['routes'][$r[0]] = '';
                }
            }
        }
    }

    /**
     *
     * @return Array $params
     */
    public static function params()
    {
        return $GLOBALS['raiseParams'];
    }

    /**
     *
     * @return Array $params
     */
    public static function db()
    {
        return $GLOBALS['dbConfig'];
    }

    /**
     *
     * @param Object $ctlr
     * @param Array $uri
     * @return Mixed
     * @throws \Exception
     */
    public function parseAction($ctlr, $uri, $ctlrName = '')
    {
        if (!empty($uri) && trim($uri[0]) && is_object($ctlr)) {
            $action = 'action';
            $acCase = $action . ucfirst($uri[0]);
            if (method_exists($ctlr, $acCase)) {
                return $ctlr->$acCase();
            } else {
                throw new \Exception('Action Not Found!');
            }
        } else {
            throw new \Exception('Action Not Found!');
        }
    }

    /**
     *
     * @param String $slang
     * @param String $cat
     * @return Mixed | boolean
     */
    public static function coreI18n($slang, $cat)
    {
        $langPath = BASEPATH . '/src/i18n/' . $slang . '.i18n.php';
        if (file_exists($langPath)) {
            if (array_key_exists('i18n', $GLOBALS['raiseParams'])) {
                return $GLOBALS['raiseParams']['i18n'][$cat];
            } else {
                $isIncluded = include $langPath;
                $GLOBALS['raiseParams']['i18n'] = $isIncluded;
                return $GLOBALS['raiseParams']['i18n'][$cat];
            }
        }
        return false;
    }

    /**
     *
     * @param String $cat
     * @param String $key
     * @param Array $params
     * @return type
     * @throws \Exception
     */
    public static function t($cat = 'app', $key = '', $params = [], $lang = '')
    {

        $slang = (empty($lang)) ? self::$lang : $lang;
        $langPath = BASEPATH . '/src/i18n/' . $slang . '/' . $cat . '.php';
        if (file_exists($langPath)) {

            $isCoreI18nExists = self::coreI18n($slang, $cat);
            $msgs = ($isCoreI18nExists !== false) ? $isCoreI18nExists : include $langPath;
            if (array_key_exists($key, $msgs)) {
                $trans = $msgs[$key];
                if (count($params) > 0) {
                    foreach ($params as $pKey => $pVal) {
                        $trans = str_replace('{{' . $pKey . '}}', $pVal, $trans);
                    }
                }
                return $trans;
            } else {
                return $key;
            }
        } else {
            
            throw new \Exception('Language Category File ' . $cat . ' Not Found!');
        }
    }

    /**
     *
     * @param String $input
     * @return Mixed
     */
    public static function cleanMe($input)
    {
        if (is_array($input)) {
            $newInput = [];
            foreach ($input as $key => $value) {
                $inputStiped = stripslashes($value);
                $inputHtml = htmlspecialchars($inputStiped, ENT_IGNORE, 'utf-8');
                $newInput[$key] = strip_tags($inputHtml);
            }
            return $newInput;
        } else {
            $inputStiped = stripslashes($input);
            $inputHtml = htmlspecialchars($inputStiped, ENT_IGNORE, 'utf-8');
            return strip_tags($inputHtml);
        }
    }

    /**
     * Method to handle the session hijacking
     */
    public function safeSession()
    {
        $remoteAddr = isset($_SESSION['REMOTE_ADDR']) ? $_SESSION['REMOTE_ADDR'] : '';
        $serverAddr = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        if (isset($remoteAddr) && !empty($remoteAddr)) {
            if ($remoteAddr != sha1($serverAddr . $userAgent)) {
                $_SESSION['PHPSESSID'] = rand(1, 1000);
                unset($_SESSION['REMOTE_ADDR']);
                header('HTTP/1.0 403 Forbidden');
                die('Session Crash.');
            }
        } else {
            $_SESSION['REMOTE_ADDR'] = sha1($serverAddr . $userAgent);
        }
    }

    public static function writeLog($filename, $data)
    {
        $filepath = BASEPATH . "/log/" . $filename . ".log";
        file_put_contents($filepath, $data, FILE_APPEND);
    }

    /**
     * callApi - send request to microservices - support multiple
     *
     * @param array  $apiCallArr | 1 or 2 level array
     * @return void
     */
    public static function callApi($apiCallArr = [])
    {
        $keys = array_keys($apiCallArr);

        if (isset($apiCallArr[$keys[0]]) && gettype($apiCallArr[$keys[0]]) != 'array') {

            $curlArr[] = $apiCallArr;
        } else {
            $curlArr = $apiCallArr;
        }

        $ip = isset(Raise::$reqHeader['Ip']) ? Raise::$reqHeader['Ip'] : getClientIP();
        $http_ref = $_SERVER["SERVER_NAME"];

        $languagesPacksAvailable = array('en', 'zh_hans', 'zh_hant');
        $selLanguage = (isset(Raise::$lang) && in_array(Raise::$lang, $languagesPacksAvailable)) ? Raise::$lang : 'zh_hans';

        //$authorization = $requestApiToken;
        $authorization = isset(Raise::$reqHeader['Token']) ? Raise::$reqHeader['Token'] : '';
        $os = isset(Raise::$reqHeader['Os']) ? Raise::$reqHeader['Os'] : '';
        $browser = isset(Raise::$reqHeader['Browser']) ? Raise::$reqHeader['Browser'] : '';
        $device_type = isset(Raise::$reqHeader['Devicetype']) ? Raise::$reqHeader['Devicetype'] : '';
        $device_id = isset(Raise::$reqHeader['Deviceid']) ? Raise::$reqHeader['Deviceid'] : '';

        $authType = 'token';
        $medium = 'app';
        $userHeaderJson = json_encode([
            "domain"=>"$http_ref",
            "location"=>getLocation($ip),
            "ip"=>"$ip",
            "browser"=>"$browser",
            "device"=>'app',
            "device_type"=>"$device_type",
            "device_id"=>"$device_id",
            "os"=>"$os"
        ]);
        
        $headers = [
            "Content-Type:application/json",
            "Token: $authorization",
            "Authtype: $authType",
            "Language: $selLanguage",
            "Ip: $ip",
            "User-Header: $userHeaderJson",
            // "Medium: $medium",
            // "Sitename:ipdemo",
        ];
        $mh = curl_multi_init();

        foreach ($curlArr as $key => $apiCall) {

            $serviceUrl = $apiCall['serviceName']; // this should depend on ser
            $command = $apiCall['command'];
            $userAccessToken = 'sampleUserToken'; // this should current login session access token
            //$param = $apiCall['param'] ?? $_REQUEST;
            $param  = issetGet($apiCall,'param',$_REQUEST);
            //print_r($apiCall['command']);
            $data = ['command' => $command,
                'param' => $param,
                'token' => $userAccessToken];

            $data = json_encode($data);

            ${'ch' . $key} = curl_init($serviceUrl);
            curl_setopt(${'ch' . $key}, CURLOPT_RETURNTRANSFER, true);
            curl_setopt(${'ch' . $key}, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt(${'ch' . $key}, CURLOPT_HTTPHEADER, $headers);
            curl_setopt(${'ch' . $key}, CURLOPT_POSTFIELDS, $data);
            curl_setopt(${'ch' . $key}, CURLOPT_TIMEOUT, 30);
            curl_setopt(${'ch' . $key}, CURLOPT_CONNECTTIMEOUT, 2); //Timeout after 7 seconds
            curl_setopt(${'ch' . $key}, CURLOPT_REFERER, $http_ref);

            curl_multi_add_handle($mh, ${'ch' . $key});

        }

        //execute the multi handle
        do {
            $status = curl_multi_exec($mh, $active);
            if ($active) {
                curl_multi_select($mh);
            }
        } while ($active && $status == CURLM_OK);

        //close the handles
        foreach ($curlArr as $key => $v) {
            curl_multi_remove_handle($mh, ${'ch' . $key});

        }

        curl_multi_close($mh);
        $response = [];
        foreach ($curlArr as $key => $v) {
            $response[] = json_decode(curl_multi_getcontent(${'ch' . $key}), true);
            // $response[] = curl_multi_getcontent(${'ch' . $key});

        }

        // var_dump($response);
        return self::parseApiData($response);

    }

    /**
     * Unwrap success api data set , retain failed one as
     *
     * @param array $curlReturnData
     * @return array
     */
    public static function parseApiData($curlReturnData)
    {
        $dataArr = [];
        foreach ($curlReturnData as $callData) {
            if (isset($callData['success'])) {
                if (isset($callData['data']['data'])) {
                    $dataArr[] = $callData['data']['data'];
                // } else if (isset($callData['data'])) {
                //     $dataArr[] = $callData['data'];
                } else {
                    $dataArr[] = $callData;
                }
            } else {
                $dataArr[] = $callData;
            }
        }
        return $dataArr;
    }
}