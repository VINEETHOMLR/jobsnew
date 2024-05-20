<?php


date_default_timezone_set('Australia/Sydney');
include 'helper.php';

$whiteList = ['127.0.0.1', '::1'];
$config = [];
$envConfig = dirname(dirname(__FILE__)) . '/.env';

if (file_exists($envConfig)) {
    $jConfig = file_get_contents($envConfig);
    $config = json_decode($jConfig, true);
    if ($config === null) {
        echo 'Error : Please check your env file, invalid json format';
        die;
    }

} else {
    echo 'Error : env file not found, please put it on root folder';
    die;
}

$apisecret = isset($config['apiSecret']) ? $config['apiSecret'] : '';
define('APISECRET', $apisecret);

define('BASEPATH', $config['basePath']);
define('ENV', $config['env']);
define('BASEURL', $config['baseUrl']);
//define('WEB_PATH', $config['baseUrl'] . 'web/');
//define('IMG_PATH', WEB_PATH . 'img/');
//Mobile Constants
//define('MOB_PATH', $config['mobWebPath']);
//define('MOB_IMG', MOB_PATH . 'img/');
define('BOUPLOADPATH', $config['boUrl'].'web/uploads/');

$userPrefix = isset($config['userPrefix']) ? $config['userPrefix'] : 's';
define('USER_PREFIX', $userPrefix);

/*coin price */
// define('TRADE_API_HTTP_URL',$config['tradeApi']['httpApiUrl']);
// define('TRADE_API_SOCKET_URL',$config['tradeApi']['socketApiUrl']);
// define('TRADE_API_USER_ID',$config['tradeApi']['userID']);
// define('TRADE_API_AUTH_CODE',$config['tradeApi']['authCode']);

$adminEmail = isset($config['adminEmail']) ? $config['adminEmail'] : 'helpdesk@example.com';
define('ADMIN_EMAIL', $adminEmail);

$dataLength = isset($config['dataLength']) ? $config['dataLength'] : 25;
define('DATA_LENGTH', $dataLength);

define('REDIS_CONNECTION',  $config['redis']['host']); // 192.168.88.200 localhost or socket
define('REDIS_NAMESPACE', $config['redis']['namespace']); // use custom prefix on all keys
define('REDIS_DB', $config['redis']['dbname']);
//define('REDIS_AUTH', $config['redis']['pass']);


//captcha setting
//phpOption & secureOption is vendor specific one, if it absent in vendor array, then no need to set.
$captchaDefaultConfig = ["vendor" => ["phpCaptcha", "secureCaptha"],
    "min_length" => 4, "max_length" => 4, "characters" => "1234567890",
    "phpOption" => ["min_font_size" => 30, "max_font_size" => 30],
    "secureOption" => ["image_width" => 120, "image_height" => 56, "math" => true],
];

$captchaConfig = isset($config['captchaConfig']) ? $config['captchaConfig'] : $captchaDefaultConfig;
// define('CAPTCHA_CONFIG', $captchaConfig);

$raiseParams = [];
$fileName = BASEPATH . '/inc/params.php';
if (file_exists($fileName)) {
    $raiseParams = include_once $fileName;
}


$file_Name = 'transactionArray.php'; $transactionArray=[];
if (file_exists(BASEPATH . '/inc/' . $file_Name)) {
    include_once $file_Name;
}




$dbConfig = $config['database']; 

$compAL = BASEPATH . '/vendor/autoload.php';
if (file_exists($compAL)) {
    include_once $compAL;
}

if (ENV === 'dev') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

spl_autoload_register(function ($class) {
    $exactClass = explode('\\', $class);
    //List of files to be skipped
    if (in_array(end($exactClass), ['PDO'])) {
        return;
    }
    if (!file_exists(str_replace('\\', '/', $class) . '.php')) {
        throw new Exception($class . " Route Not Found", 404);
    } else {
        include_once str_replace('\\', '/', $class) . '.php';
    }
});