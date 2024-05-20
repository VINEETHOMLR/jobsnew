<?php

namespace src\lib;
use inc\Raise;

class Helper {

    protected $method = 'aes-128-ctr';

    private $key;

    public function __construct($key = FALSE, $method = FALSE)
    {
        if (!$key) {
            $key = php_uname(); // default encryption key if none supplied
        }
        // convert ASCII keys to binary format
        $this->key = (ctype_print($key)) ? openssl_digest($key, 'SHA256', TRUE) : $key;

        if ($method) {
            if (in_array(strtolower($method), openssl_get_cipher_methods())) {
                $this->method = $method;
            } else {
                die(__METHOD__ . ": unrecognised cipher method: {$method}");
            }
        }
    }
    
    /**
     *
     * @return Boolean
     */
    public static function checkLogin()
    {
        // return isset($_SESSION['USER_ID']);
        return isset(Raise::$userObj) && !empty(Raise::$userObj);
    }
    
    /**
     * 
     * @param String $type
     * @return boolean
     */
    public static function checkUserAgent($type = NULL) {
        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        if ($type == 'bot') {
            if (preg_match("/googlebot|adsbot|yahooseeker|yahoobot|msnbot|watchmouse|pingdom\.com|feedfetcher-google/", $user_agent)) {
                return true;
            }
        } else if ($type == 'browser') {
            if (preg_match("/mozilla\/|opera\//", $user_agent)) {
                return true;
            }
        } else if ($type == 'mobile') {
            if (preg_match("/phone|iphone|itouch|ipod|symbian|android|htc_|htc-|palmos|blackberry|opera mini|iemobile|windows ce|nokia|fennec|hiptop|kindle|mot |mot-|webos\/|samsung|sonyericsson|^sie-|nintendo/", $user_agent)) {
                return true;
            } else if (preg_match("/mobile|pda;|avantgo|eudoraweb|minimo|netfront|brew|teleca|lg;|lge |wap;| wap /", $user_agent)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Method that returns the LONG formatted IP Address
     * @return Integer
     */
    public static function getIP() {
        $ipAddr = $_SERVER['REMOTE_ADDR'];
        return ip2long($ipAddr);
    }

    /**
     * 
     * @param String $msg
     */
    public static function setInfo($msg) {
        $unID = uniqid() . rand(1, 99);
        $_SESSION['alertInfo'] = '<div id="' . $unID . '" class="alert alert-primary" role="alert">' . $msg . '</div><script>setTimeout(function(){ document.getElementById("' . $unID . '").style.display = "none"; }, 5000);</script>';
    }

    /**
     * 
     * @return String
     */
    public static function getInfo() {
        if (isset($_SESSION['alertInfo'])) {
            $info = $_SESSION['alertInfo'];
            unset($_SESSION['alertInfo']);
            return $info;
        } else {
            return '';
        }
    }

    //return secure random str
    public static function strRandom($length){

        try {
            return bin2hex(random_bytes($length/2));
        } catch (TypeError $e) {
            // Well, it's an integer, so this IS unexpected.
            die("An unexpected error has occurred"); 
        } catch (Error $e) {
            // This is also unexpected because 32 is a reasonable integer.
            die("An unexpected error has occurred");
        } catch (Exception $e) {
            // If you get this message, the CSPRNG failed hard.
            die("Could not generate a random string. Is our OS secure?");
        }
    
    }
    /**
     * Helper to build assoc array for callApi
     *
     * @param string $serviceName
     * @param string $command
     * @param array $param
     * @param boolean $isTransaction
     * @return array
     */
    public static function buildParam($serviceName, $command, $param = [], $isTransaction = false)
    {
        return compact('serviceName', 'command', 'param', "isTransaction");
    }

    public static function getUserSystemInfo() {

        $info['ip'] = isset(Raise::$reqHeader['Ip']) ? Raise::$reqHeader['Ip'] : getClientIP();
        $info['domain'] = $_SERVER["SERVER_NAME"];

        $languagesPacksAvailable = array('en', 'zh_hans', 'zh_hant');
        $info['language']  = (isset(Raise::$lang) && in_array(Raise::$lang, $languagesPacksAvailable)) ? Raise::$lang : 'zh_hans';

        $info['token'] = isset(Raise::$reqHeader['Token']) ? Raise::$reqHeader['Token'] : '';
        $info['device_os'] = isset(Raise::$reqHeader['Os']) ? Raise::$reqHeader['Os'] : '';
        $info['browser'] = isset(Raise::$reqHeader['Browser']) ? Raise::$reqHeader['Browser'] : '';
        $info['device_type'] = isset(Raise::$reqHeader['Devicetype']) ? Raise::$reqHeader['Devicetype'] : '';
        $info['device_id'] = isset(Raise::$reqHeader['Deviceid']) ? Raise::$reqHeader['Deviceid'] : '';
        $info['device_model'] = isset(Raise::$reqHeader['Devicemodel']) ? Raise::$reqHeader['Devicemodel'] : '';
        $info['device_imei'] = isset(Raise::$reqHeader['Deviceimei']) ? Raise::$reqHeader['Deviceimei'] : '';
        $info['device_manufacturer'] = isset(Raise::$reqHeader['Devicemanufacturer']) ? Raise::$reqHeader['Devicemanufacturer'] : '';
        $info['device_appversion'] = isset(Raise::$reqHeader['Deviceappversion']) ? Raise::$reqHeader['Deviceappversion'] : '';
        
        $info['device_location'] = isset(Raise::$reqHeader['Devicelocation']) ? Raise::$reqHeader['Devicelocation'] : '';

        return $info;
    }

    /**
     * 
     * @param String $data
     * @return String Encrypted
     */
    public function encrypt($data)
    {
        $iv = openssl_random_pseudo_bytes($this->iv_bytes());
        return bin2hex($iv) . openssl_encrypt($data, $this->method, $this->key, 0, $iv);
    }

    /**
     * 
     * @param String $data Encrypted Data
     * @return boolean | Decrypted Text
     */
    public function decrypt($data)
    {
        $iv_strlen = 2 * $this->iv_bytes();
        if (preg_match("/^(.{" . $iv_strlen . "})(.+)$/", $data, $regs)) {
            list(, $iv, $crypted_string) = $regs;
            if (ctype_xdigit($iv) && strlen($iv) % 2 == 0) {
                return openssl_decrypt($crypted_string, $this->method, $this->key, 0, hex2bin($iv));
            }
        }
        return FALSE; // failed to decrypt
    }


    protected function iv_bytes()
    {
        return openssl_cipher_iv_length($this->method);
    }
}
