<?php
require_once 'vendor/autoload.php';
use inc\Raise;
use src\lib\Router;
use GeoIp2\Database\Reader;

require 'src/lib/ipdb/ipip/db/City.php';

/**
 * Method to check is logged in
 * @return bool
 */
function notLoggedIn()
{
    return isset($_SESSION) ? !issetNotEmpty($_SESSION, 'USER_ID') : false;
}
/**
 * Method to get time stamp
 * @param Date $date
 * @return timestamp
 */
function getTimeStamp($date = '')
{
    return (new \DateTime($date == '' ? 'now' : $date))->getTimeStamp();
}
/**
 * Method to get post params
 * @param String $key
 * @return any
 */
function postPrm($key)
{
    return Router::post($key);
}
/**
 * Method to get query params
 * @param String $key
 */
function getPrm($key)
{
    return Router::req($key);
}
/**
 * Method to get all query params
 */
function getPrms()
{
    return Router::reqAll();
}

function getRouterGetAll()
{
    return Router::getAll();
}
/**
 * Method to check is POST method
 */
function isPOST()
{
    return Router::getReqMethod() == 'post';
}
/**
 * Method to check is GET method
 */
function isGET()
{
    return (new src\lib\Router)->getReqMethod() == 'get';
}

/**
 * Method to get model instance
 * @param String $name
 */
function getModel($name)
{
    $mdl = "\src\models\\$name";
    return new $mdl;
}

function issetNotEmpty($prms, $fld)
{
    return isset($prms[$fld]) && !empty($prms[$fld]);
}

function issetGet($prms, $fld, $default)
{
    return (isset($prms[$fld]) && !empty($prms[$fld])) ? $prms[$fld] : $default;
}

/**
 * Method to print the debug
 * @param Mixed $prms
 */
function dd(...$prms)
{
    echo '<pre>';
    var_dump($prms);
    echo '</pre>';
    die;
}

/**
 * Method to alias of Raise::t
 * @param String $folder
 * @param String $key
 * @param Array $arr
 * @return String
 */
function t($folder, $key, $arr = [])
{
    return Raise::t($folder, $key, $arr);
}

/**
 *
 * @param String $input
 * @return Mixed
 */
function cleanMe($input)
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

//HEX2RGB
if (!function_exists('hex2rgb')) {

    function hex2rgb($hex_str, $return_string = false, $separator = ',')
    {
        $hex_str = preg_replace("/[^0-9A-Fa-f]/", '', $hex_str); // Gets a proper hex string
        $rgb_array = [];
        if (strlen($hex_str) == 6) {
            $color_val = hexdec($hex_str);
            $rgb_array['r'] = 0xFF & ($color_val >> 0x10);
            $rgb_array['g'] = 0xFF & ($color_val >> 0x8);
            $rgb_array['b'] = 0xFF & $color_val;
        } elseif (strlen($hex_str) == 3) {
            $rgb_array['r'] = hexdec(str_repeat(substr($hex_str, 0, 1), 2));
            $rgb_array['g'] = hexdec(str_repeat(substr($hex_str, 1, 1), 2));
            $rgb_array['b'] = hexdec(str_repeat(substr($hex_str, 2, 1), 2));
        } else {
            return false;
        }
        return $return_string ? implode($separator, $rgb_array) : $rgb_array;
    }

}

/**
 * Method to identify the request is from Mobile
 * @return boolean
 */
function isMobi()
{
    $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if (preg_match("/phone|iphone|itouch|ipod|symbian|android|htc_|htc-|palmos|blackberry|opera mini|iemobile|windows ce|nokia|fennec|hiptop|kindle|mot |mot-|webos\/|samsung|sonyericsson|^sie-|nintendo/", $user_agent)) {
        // these are the most common
        return true;
    } elseif (preg_match("/mobile|pda;|avantgo|eudoraweb|minimo|netfront|brew|teleca|lg;|lge |wap;| wap /", $user_agent)) {
        // these are less common, and might not be worth checking
        return true;
    }
    return false;
}

// Function to get the client IP address
function getClientIP()
{
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    } else {
        $ipaddress = '127.0.0.1';
    }

    if ($ipaddress == "::1") {
        $ipaddress = "127.0.0.1";
    }

    return $ipaddress;
}

function sendNotification($prms)
{
    if (!isset($prms['player_id']) || !isset($prms['group_id']) || !isset($prms['content'])) {
        return false;
    }
    $notification = getModel('Notification');
    $notification->player_id = $prms['player_id'];
    $notification->group_id = $prms['group_id'];
    $notification->content = $prms['content'];
    return $notification->save();
}

function arrayOnly($keys, $arr)
{
    $tmp = [];
    foreach ($keys as $key) {
        if (isset($arr[$key])) {
            $tmp[$key] = $arr[$key];
        }
    }
    return $tmp;
}

function convertMimeToExt($mime)
{
    if ($mime == 'image/jpeg') {
        $extension = '.jpg';
    } elseif ($mime == 'image/png') {
        $extension = '.png';
    } elseif ($mime == 'image/gif') {
        $extension = '.gif';
    } else {
        $extension = '';
    }

    return $extension;
}

/**
 * @param $user_agent null
 * @return string
 */
function getOS($user_agent = null)
{
    if (!isset($user_agent) && isset($_SERVER['HTTP_USER_AGENT'])) {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
    }

    // https://stackoverflow.com/questions/18070154/get-operating-system-info-with-php
    $os_array = [
        'windows nt 10' => 'Windows 10',
        'windows nt 6.3' => 'Windows 8.1',
        'windows nt 6.2' => 'Windows 8',
        'windows nt 6.1|windows nt 7.0' => 'Windows 7',
        'windows nt 6.0' => 'Windows Vista',
        'windows nt 5.2' => 'Windows Server 2003/XP x64',
        'windows nt 5.1' => 'Windows XP',
        'windows xp' => 'Windows XP',
        'windows nt 5.0|windows nt5.1|windows 2000' => 'Windows 2000',
        'windows me' => 'Windows ME',
        'windows nt 4.0|winnt4.0' => 'Windows NT',
        'windows ce' => 'Windows CE',
        'windows 98|win98' => 'Windows 98',
        'windows 95|win95' => 'Windows 95',
        'win16' => 'Windows 3.11',
        'iphone' => 'iOS',
        'ipod' => 'iOS',
        'ipad' => 'iOS',
        'android' => 'Android',
        'mac os x 10.1[^0-9]' => 'Mac OS X Puma',
        'macintosh|mac os x' => 'Mac OS X',
        'mac_powerpc' => 'Mac OS 9',
        'linux' => 'Linux',
        'ubuntu' => 'Linux - Ubuntu',
        'blackberry' => 'BlackBerry',
        'webos' => 'Mobile',

        '(media center pc).([0-9]{1,2}\.[0-9]{1,2})' => 'Windows Media Center',
        '(win)([0-9]{1,2}\.[0-9x]{1,2})' => 'Windows',
        '(win)([0-9]{2})' => 'Windows',
        '(windows)([0-9x]{2})' => 'Windows',

        // Doesn't seem like these are necessary...not totally sure though..
        //'(winnt)([0-9]{1,2}\.[0-9]{1,2}){0,1}'=>'Windows NT',
        //'(windows nt)(([0-9]{1,2}\.[0-9]{1,2}){0,1})'=>'Windows NT', // fix by bg

        'Win 9x 4.90' => 'Windows ME',
        '(windows)([0-9]{1,2}\.[0-9]{1,2})' => 'Windows',
        'win32' => 'Windows',
        '(java)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2})' => 'Java',
        '(Solaris)([0-9]{1,2}\.[0-9x]{1,2}){0,1}' => 'Solaris',
        'dos x86' => 'DOS',
        'Mac OS X' => 'Mac OS X',
        'Mac_PowerPC' => 'Macintosh PowerPC',
        '(mac|Macintosh)' => 'Mac OS',
        '(sunos)([0-9]{1,2}\.[0-9]{1,2}){0,1}' => 'SunOS',
        '(beos)([0-9]{1,2}\.[0-9]{1,2}){0,1}' => 'BeOS',
        '(risc os)([0-9]{1,2}\.[0-9]{1,2})' => 'RISC OS',
        'unix' => 'Unix',
        'os/2' => 'OS/2',
        'freebsd' => 'FreeBSD',
        'openbsd' => 'OpenBSD',
        'netbsd' => 'NetBSD',
        'irix' => 'IRIX',
        'plan9' => 'Plan9',
        'osf' => 'OSF',
        'aix' => 'AIX',
        'GNU Hurd' => 'GNU Hurd',
        '(fedora)' => 'Linux - Fedora',
        '(kubuntu)' => 'Linux - Kubuntu',
        '(ubuntu)' => 'Linux - Ubuntu',
        '(debian)' => 'Linux - Debian',
        '(CentOS)' => 'Linux - CentOS',
        '(Mandriva).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)' => 'Linux - Mandriva',
        '(SUSE).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)' => 'Linux - SUSE',
        '(Dropline)' => 'Linux - Slackware (Dropline GNOME)',
        '(ASPLinux)' => 'Linux - ASPLinux',
        '(Red Hat)' => 'Linux - Red Hat',
        // Loads of Linux machines will be detected as unix.
        // Actually, all of the linux machines I've checked have the 'X11' in the User Agent.
        //'X11'=>'Unix',
        '(linux)' => 'Linux',
        '(amigaos)([0-9]{1,2}\.[0-9]{1,2})' => 'AmigaOS',
        'amiga-aweb' => 'AmigaOS',
        'amiga' => 'Amiga',
        'AvantGo' => 'PalmOS',
        //'(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1}-([0-9]{1,2}) i([0-9]{1})86){1}'=>'Linux',
        //'(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1} i([0-9]{1}86)){1}'=>'Linux',
        //'(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1})'=>'Linux',
        '[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}' => 'Linux',
        '(webtv)/([0-9]{1,2}\.[0-9]{1,2})' => 'WebTV',
        'Dreamcast' => 'Dreamcast OS',
        'GetRight' => 'Windows',
        'go!zilla' => 'Windows',
        'gozilla' => 'Windows',
        'gulliver' => 'Windows',
        'ia archiver' => 'Windows',
        'NetPositive' => 'Windows',
        'mass downloader' => 'Windows',
        'microsoft' => 'Windows',
        'offline explorer' => 'Windows',
        'teleport' => 'Windows',
        'web downloader' => 'Windows',
        'webcapture' => 'Windows',
        'webcollage' => 'Windows',
        'webcopier' => 'Windows',
        'webstripper' => 'Windows',
        'webzip' => 'Windows',
        'wget' => 'Windows',
        'Java' => 'Unknown',
        'flashget' => 'Windows',

        // delete next line if the script show not the right OS
        //'(PHP)/([0-9]{1,2}.[0-9]{1,2})'=>'PHP',
        'MS FrontPage' => 'Windows',
        '(msproxy)/([0-9]{1,2}.[0-9]{1,2})' => 'Windows',
        '(msie)([0-9]{1,2}.[0-9]{1,2})' => 'Windows',
        'libwww-perl' => 'Unix',
        'UP.Browser' => 'Windows CE',
        'NetAnts' => 'Windows'
    ];

    // https://github.com/ahmad-sa3d/php-useragent/blob/master/core/user_agent.php
    $arch_regex = '/\b(x86_64|x86-64|Win64|WOW64|x64|ia64|amd64|ppc64|sparc64|IRIX64)\b/ix';
    $arch = preg_match($arch_regex, $user_agent) ? '64' : '32';

    foreach ($os_array as $regex => $value) {
        if (preg_match('{\b(' . $regex . ')\b}i', $user_agent)) {
            return $value . ' x' . $arch;
        }
    }
    return 'Unknown';
}

function getBrowser($user_agent = null)
{
    if (!isset($user_agent) && isset($_SERVER['HTTP_USER_AGENT'])) {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
    }

    $browser = "Unknown Browser";

    $browser_array = [
        '/msie/i' => 'Internet Explorer',
        '/chrome/i' => 'Chrome',
        '/firefox/i' => 'Firefox',
        '/safari/i' => 'Safari',
        '/edge/i' => 'Edge',
        '/opera/i' => 'Opera',
        '/netscape/i' => 'Netscape',
        '/maxthon/i' => 'Maxthon',
        '/konqueror/i' => 'Konqueror'
        // '/mobile/i'    => 'Handheld Browser'
    ];

    foreach ($browser_array as $regex => $value) {
        if (preg_match($regex, $user_agent)) {
            return $value;
        }
    }

    return $browser;
}

function checkUserAgent($type = null)
{
    if (!isset($_SERVER['HTTP_USER_AGENT'])) {
        return false;
    }

    $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if ($type == 'bot') {
        // matches popular bots
        if (preg_match("/googlebot|adsbot|yahooseeker|yahoobot|msnbot|watchmouse|pingdom\.com|feedfetcher-google/", $user_agent)) {
            return true;
            // watchmouse|pingdom\.com are "uptime services"
        }
    } elseif ($type == 'browser') {
        // matches core browser types
        if (preg_match("/mozilla\/|opera\//", $user_agent)) {
            return true;
        }
    } elseif ($type == 'mobile') {
        // matches popular mobile devices that have small screens and/or touch inputs
        // mobile devices have regional trends; some of these will have varying popularity in Europe, Asia, and America
        // detailed demographics are unknown, and South America, the Pacific Islands, and Africa trends might not be represented, here
        if (preg_match("/phone|iphone|itouch|ipod|symbian|android|htc_|htc-|palmos|blackberry|opera mini|iemobile|windows ce|nokia|fennec|hiptop|kindle|mot |mot-|webos\/|samsung|sonyericsson|^sie-|nintendo/", $user_agent)) {
            // these are the most common
            return true;
        } elseif (preg_match("/mobile|pda;|avantgo|eudoraweb|minimo|netfront|brew|teleca|lg;|lge |wap;| wap /", $user_agent)) {
            // these are less common, and might not be worth checking
            return true;
        }
    }
    return false;
}

function getLocation($ip) {
    $country_name = "";
    $reader = new Reader('src/lib/ipdb/GeoIP2-City.mmdb'); 

    if($ip != '' &&  $ip != '127.0.0.1')
    {
        $record = $reader->city($ip);

        if(isset($record->city->names['zh-CN']) && $record->city->names['zh-CN'] != '' )
            $country_name .= $record->city->names['zh-CN']." ,";
        if(isset($record->mostSpecificSubdivision->names['zh-CN']) && $record->mostSpecificSubdivision->names['zh-CN'] != '' )
            $country_name .= $record->mostSpecificSubdivision->names['zh-CN']." ,";
        if(isset($record->country->names['zh-CN']) && $record->country->names['zh-CN'] != '' )
            $country_name .= $record->country->names['zh-CN']." ,";
       
        $country_name = rtrim($country_name, ',');
    } else if ($ip == '127.0.0.1') {
        $country_name = "本机地址";
    }
    
    return $country_name;
}

function datetimeFormat($created_time)
{
    $today = strtotime(date('Y-m-d H:i:s'));

    // It returns the time difference in Seconds...
    $time_differnce = $today - $created_time;

    // To Calculate the time difference in Years...
    $years = 60 * 60 * 24 * 365;

    // To Calculate the time difference in Months...
    $months = 60 * 60 * 24 * 30;

    // To Calculate the time difference in Days...
    $days = 60 * 60 * 24;

    // To Calculate the time difference in Hours...
    $hours = 60 * 60;

    // To Calculate the time difference in Minutes...
    $minutes = 60;

    // if(intval($time_differnce/$years) > 1)
    // {
    //     return intval($time_differnce/$years)." years ago";
    // }else if(intval($time_differnce/$years) > 0)
    // {
    //     return intval($time_differnce/$years)." year ago";
    // }else if(intval($time_differnce/$months) > 1)
    // {
    //     return intval($time_differnce/$months)." months ago";
    // }
    // if(intval($time_differnce/$months) > 1)
    // {
    return date('Y-m-d h:i A', $created_time);
    // }
    // else if(intval(($time_differnce/$months)) > 0)
    // {
    //     return intval(($time_differnce/$months))." month ago";
    // }else if(intval(($time_differnce/$days)) > 1)
    // {
    //     return intval(($time_differnce/$days))." days ago";
    // }else if (intval(($time_differnce/$days)) > 0)
    // {
    //     return intval(($time_differnce/$days))." day ago";
    // }else if (intval(($time_differnce/$hours)) > 1)
    // {
    //     return intval(($time_differnce/$hours))." hours ago";
    // }else if (intval(($time_differnce/$hours)) > 0)
    // {
    //     return intval(($time_differnce/$hours))." hour ago";
    // }else if (intval(($time_differnce/$minutes)) > 1)
    // {
    //     return intval(($time_differnce/$minutes))." minutes ago";
    // }else if (intval(($time_differnce/$minutes)) > 0)
    // {
    //     return intval(($time_differnce/$minutes))." minute ago";
    // }else if (intval(($time_differnce)) > 1)
    // {
    //     return intval(($time_differnce))." seconds ago";
    // }else
    // {
    //     return "few seconds ago";
    // }
}

function currencyFormat($money)
{
    return number_format($money, 2, '.', ',');
}

/**
 * Round down to 2 decimal point. Use for displaying amount.
 *
 * @param float $amount
 * @return string
 */
function amountDisplay($amount = 0.00)
{
    return number_format((floor($amount * 100) / 100), 2, ".", "");
}

function getallheaders_new()
{
   $headers = [];
   foreach($_SERVER as $name => $value)
   {
       if (substr($name, 0, 5) == 'HTTP_')
       {
           $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
       }
   }

   return $headers;
}